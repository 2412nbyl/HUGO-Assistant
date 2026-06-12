<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\User::all() as $user) {
    echo "User: " . $user->name . " | Avatar: " . $user->avatar_url . "\n";
}
