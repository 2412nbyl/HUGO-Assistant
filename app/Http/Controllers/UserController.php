<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function createForm()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        return view('users.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:admin,notaris,staff,freelancer',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);
        return back()->with('message','Akun berhasil ditambahkan ✓');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $user = User::withTrashed()->findOrFail($id);

        $rules = [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|string|in:admin,notaris,staff,freelancer',
        ];

        // Only validate password if one is provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        $data = $request->validate($rules);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        
        \App\Models\AuditTrail::log('users', $user->id, 'updated', null, ['name' => $user->name, 'role' => $user->role]);

        return back()->with('message','Akun berhasil diperbarui ✓');
    }


    public function manage()
    {
        if (!in_array(auth()->user()->role, ['admin','notaris'])) abort(403);
        $users = User::withTrashed()->latest()->get();
        return view('users.manage', compact('users'));
    }

    public function destroy(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return back()->with('message','Tidak dapat menghapus diri sendiri');
        \App\Models\AuditTrail::log('users', $user->id, 'deleted', ['name' => $user->name, 'role' => $user->role], null);
        $user->delete(); // hard delete
        return back()->with('message','Akun berhasil dihapus');
    }

    /** Soft deactivate: user cannot login, data preserved */
    public function deactivate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return response()->json(['error' => 'Tidak dapat menonaktifkan diri sendiri.'], 422);
        $user->update(['is_active' => false]);
        $user->delete(); // soft delete so they can't login
        \App\Models\AuditTrail::log('users', $user->id, 'deactivated', ['is_active' => true], ['is_active' => false]);
        return response()->json(['success' => true]);
    }

    /** Restore deactivated account */
    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        $user->update(['is_active' => true]);
        \App\Models\AuditTrail::log('users', $user->id, 'restored', ['is_active' => false], ['is_active' => true]);
        return response()->json(['success' => true]);
    }


    /**
     * Upload & save avatar from crop modal (base64 data URL)
     * Saves to storage/app/public/avatars/ via Laravel Storage
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate(['image' => 'required|string']);

        $dataUrl = $request->input('image');

        // Parse base64 data URL
        if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            return response()->json(['success' => false, 'message' => 'Format gambar tidak valid'], 422);
        }

        $ext    = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $binary = base64_decode($base64);

        if ($binary === false || strlen($binary) < 100) {
            return response()->json(['success' => false, 'message' => 'Data gambar tidak valid'], 422);
        }

        // Ensure storage symlink exists (Windows-safe fallback)
        $linkPath = public_path('storage');
        $target   = storage_path('app/public');
        if (!file_exists($linkPath)) {
            // Try symlink first, fall back to copy directory
            @symlink($target, $linkPath);
        }

        // Save via Storage facade to storage/app/public/avatars/
        $filename     = 'avatars/' . uniqid('avatar_') . '.' . $ext;
        $saved        = \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $binary);

        if (!$saved) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan file ke storage'], 500);
        }

        $user = auth()->user();
        $url  = url('/storage/' . $filename);

        // Delete old avatar from storage
        if ($user->avatar_url && strpos($user->avatar_url, '/storage/avatars/') !== false) {
            $oldPath = 'avatars/' . basename(parse_url($user->avatar_url, PHP_URL_PATH));
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }

        // Also clean up any old public/uploads/ avatar
        if ($user->avatar_url && strpos($user->avatar_url, '/uploads/avatars/') !== false) {
            $oldFile = public_path('uploads/avatars/' . basename(parse_url($user->avatar_url, PHP_URL_PATH)));
            if (file_exists($oldFile)) @unlink($oldFile);
        }

        $user->avatar_url = $url;
        $user->save();

        return response()->json(['success' => true, 'url' => $url]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kata sandi sekarang salah'], 422);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        \App\Models\AuditTrail::log('users', $user->id, 'password_changed');

        return response()->json(['success' => true]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $old = ['name' => $user->name, 'email' => $user->email];
        
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);
        
        \App\Models\AuditTrail::log('users', $user->id, 'profile_updated', $old, $validated);

        return response()->json(['success' => true, 'name' => $user->name, 'email' => $user->email]);
    }
}
