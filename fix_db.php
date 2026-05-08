<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasColumn('chat_messages', 'receiver_id')) {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
        });
        echo "Added receiver_id successfully.\n";
    } else {
        echo "receiver_id already exists.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
