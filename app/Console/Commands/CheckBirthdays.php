<?php

namespace App\Console\Commands;

use App\Models\NotarisCase;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Console\Command;

class CheckBirthdays extends Command
{
    protected $signature   = 'birthdays:check';
    protected $description = 'Check client birthdays and post notifications to the chat';

    public function handle(): void
    {
        $cases = NotarisCase::whereNotNull('birth_date')->get();
        $today = now();
        $notified = 0;

        foreach ($cases as $case) {
            $days = $case->daysUntilBirthday();

            // Only broadcast on the actual birthday
            if ($days === 0) {
                // Check if we already sent a birthday message today
                $alreadySent = ChatMessage::where('type', 'system')
                    ->where('message', 'like', "%{$case->client_name}% ulang tahun%")
                    ->whereDate('created_at', $today->toDateString())
                    ->exists();

                if (!$alreadySent) {
                    // Find a system user (first admin) to send as
                    $admin = User::where('role', 'admin')->first();

                    ChatMessage::create([
                        'sender_id' => $admin?->id ?? 1,
                        'message'   => "🎂 Selamat Ulang Tahun untuk klien {$case->client_name} (Kasus: {$case->case_name})! Hari ini adalah hari spesial mereka.",
                        'type'      => 'system',
                        'meta'      => ['case_id' => $case->id, 'birthday' => true],
                    ]);

                    $notified++;
                    $this->info("Birthday broadcast sent for: {$case->client_name}");
                }
            }
        }

        $this->info("Birthday check complete. {$notified} notification(s) sent.");
    }
}
