<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChatController extends Controller
{
    /**
     * Show the full-page chat UI.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $receiverId = $request->query('user_id');

        // Subquery to get the last message timestamp for each contact
        $lastMessages = \DB::table('chat_messages')
            ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as contact_id', [$userId])
            ->selectRaw('MAX(created_at) as last_msg_at')
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->groupBy('contact_id');

        // Get all active users, sorted by the timestamp of the last message exchanged
        $users = User::where('id', '!=', $userId)
            ->where('is_active', true)
            ->leftJoinSub($lastMessages, 'last_messages', function($join) {
                $join->on('users.id', '=', 'last_messages.contact_id');
            })
            ->select('users.*', 'last_messages.last_msg_at')
            ->orderByDesc('last_messages.last_msg_at')
            ->orderBy('users.name', 'ASC')
            ->get();

        // Build query for messages
        $query = ChatMessage::with(['sender', 'receiver']);

        if ($receiverId) {
            // Private chat messages between current user and receiver
            $query->where(function($q) use ($userId, $receiverId) {
                $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
            })->orWhere(function($q) use ($userId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
            });
        } else {
            // Global chat messages — strictly exclude system/private notifications
            $query->whereNull('receiver_id')->whereNotIn('type', ['request', 'approval']);
        }

        $messages = $query->orderBy('id', 'asc')->take(100)->get();
        $activeReceiver = $receiverId ? User::find($receiverId) : null;

        return view('chat.index', compact('messages', 'users', 'activeReceiver', 'receiverId'));
    }

    /**
     * Store a new chat message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $msg = ChatMessage::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'type'        => 'message',
        ]);
        $msg->load(['sender', 'receiver']);

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($msg),
        ]);
    }

    /**
     * Poll for new messages.
     */
    public function poll(Request $request)
    {
        $lastId     = (int) $request->query('last_id', 0);
        $isBadge    = $request->query('badge', 0);
        $receiverId = $request->query('user_id'); // If missing, assumes global

        $userId = auth()->id();

        $query = ChatMessage::with(['sender', 'receiver'])
            ->where('id', '>', $lastId);

        if ($receiverId) {
            $query->where(function($q) use ($userId, $receiverId) {
                $q->where(function($sq) use ($userId, $receiverId) {
                    $sq->where('sender_id', $userId)->where('receiver_id', $receiverId);
                })->orWhere(function($sq) use ($userId, $receiverId) {
                    $sq->where('sender_id', $receiverId)->where('receiver_id', $userId);
                });
            });
        } elseif ($isBadge == 0) {
            // Only pull global messages if not polling for badge
            $query->whereNull('receiver_id');
        }

        $messages = $query->orderBy('id', 'asc')->get();
        $lastIdNew = $messages->isNotEmpty() ? $messages->last()->id : $lastId;

        if ($request->boolean('contact_badges')) {
            $reads = $request->input('reads', []);
            if (is_string($reads)) {
                $decoded = json_decode($reads, true);
                $reads = is_array($decoded) ? $decoded : [];
            }

            $globalRead = (int) ($reads['global'] ?? 0);
            $badges = [
                'global' => ChatMessage::where('id', '>', $globalRead)
                    ->whereNull('receiver_id')
                    ->where('sender_id', '!=', $userId)
                    ->whereNotIn('type', ['request', 'approval'])
                    ->count(),
            ];

            $contactIds = ChatMessage::query()
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
                })
                ->whereNotNull('receiver_id')
                ->get()
                ->flatMap(fn ($m) => [$m->sender_id, $m->receiver_id])
                ->filter(fn ($id) => $id && (int) $id !== (int) $userId)
                ->unique()
                ->values();

            foreach ($contactIds as $contactId) {
                $contactRead = (int) ($reads[(string) $contactId] ?? 0);
                $badges[(string) $contactId] = ChatMessage::where('id', '>', $contactRead)
                    ->where('sender_id', $contactId)
                    ->where('receiver_id', $userId)
                    ->whereIn('type', ['message', 'approval'])
                    ->count();
            }

            return response()->json(['badges' => $badges]);
        }

        if ($isBadge) {
            $reads = $request->input('reads', []);
            if (is_string($reads)) {
                $decoded = json_decode($reads, true);
                $reads = is_array($decoded) ? $decoded : [];
            }

            // Unread global count
            $globalRead = (int) ($reads['global'] ?? 0);
            $unreadGlobal = ChatMessage::where('id', '>', $globalRead)
                ->whereNull('receiver_id')
                ->where('sender_id', '!=', $userId)
                ->whereNotIn('type', ['request', 'approval'])
                ->count();

            // Unread private count
            $unreadPrivate = 0;
            $senders = ChatMessage::where('receiver_id', $userId)
                ->where('sender_id', '!=', $userId)
                ->select('sender_id')
                ->groupBy('sender_id')
                ->get()
                ->pluck('sender_id');

            foreach ($senders as $senderId) {
                $lastReadForSender = (int) ($reads[(string) $senderId] ?? 0);
                $unreadPrivate += ChatMessage::where('receiver_id', $userId)
                    ->where('sender_id', $senderId)
                    ->where('id', '>', $lastReadForSender)
                    ->count();
            }

            $unreadCount = $unreadGlobal + $unreadPrivate;

            // Return latest messages for global popup notifications
            $latestMessages = ChatMessage::with('sender')
                ->where('id', '>', $lastId)
                ->where('sender_id', '!=', $userId)
                ->where(function($q) use ($userId) {
                    $q->whereNull('receiver_id')->orWhere('receiver_id', $userId);
                })->get()->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->sender->name,
                    'message' => $m->message,
                    'is_private' => $m->receiver_id !== null
                ]);

            return response()->json([
                'unread' => $unreadCount,
                'last_id' => $lastIdNew,
                'latest' => $latestMessages
            ]);
        }

        return response()->json([
            'messages' => $messages->map(fn($m) => $this->formatMessage($m)),
            'last_id'  => $lastIdNew,
        ]);
    }

    /**
     * Admin approves a password reset request.
     */
    public function approve(Request $request, int $id)
    {
        // Only admin/notaris can approve
        if (!in_array(auth()->user()->role, ['admin', 'notaris'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $requestMsg = ChatMessage::findOrFail($id);
        if ($requestMsg->type !== 'request' || $requestMsg->is_approved !== null) {
            return response()->json(['success' => false, 'message' => 'Permintaan tidak valid'], 422);
        }

        $meta = $requestMsg->meta ?? [];
        $userId = $meta['user_id'] ?? null;
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Data user tidak ditemukan'], 422);
        }

        $newPass = $request->input('password');
        if (!$newPass) {
            return response()->json(['success' => false, 'message' => 'Kata sandi baru diperlukan'], 422);
        }
        if (strlen($newPass) < 6) {
            return response()->json(['success' => false, 'message' => 'Sandi minimal 6 karakter'], 422);
        }

        $user = User::find($userId);
        if ($user) {
            $user->password = Hash::make($newPass);
            $user->save();
        }

        // Mark the request as approved
        $requestMsg->is_approved = true;
        $requestMsg->save();

        // Post approval system message (PRIVATE to the requester)
        ChatMessage::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $userId,
            'message'     => "✅ Permintaan reset sandi untuk @{$user->username} telah disetujui oleh " . auth()->user()->name . ". Sandi baru: {$newPass}",
            'type'        => 'approval',
            'meta'        => ['for_user_id' => $userId, 'temp_pass' => $newPass],
        ]);

        return response()->json(['success' => true, 'temp_password' => $newPass]);
    }

    private function formatMessage(ChatMessage $msg): array
    {
        return [
            'id'          => $msg->id,
            'message'     => $msg->message,
            'type'        => $msg->type,
            'is_approved' => $msg->is_approved,
            'meta'        => $msg->meta,
            'receiver_id' => $msg->receiver_id,
            'sender'      => [
                'id'   => $msg->sender->id,
                'name' => $msg->sender->name,
                'role' => $msg->sender->role,
            ],
            'created_at' => $msg->created_at->format('H:i'),
        ];
    }
}
