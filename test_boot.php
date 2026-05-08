<?php
echo "Loading autoloader...\n";
require __DIR__.'/vendor/autoload.php';
echo "Loading app...\n";
$app = require_once __DIR__.'/bootstrap/app.php';
echo "App loaded. Handling console...\n";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "Kernel made.\n";
echo "Done.\n";
