<?php
function search_dir($dir, $pattern) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        if (strpos($path, 'node_modules') !== false || strpos($path, 'vendor') !== false || strpos($path, '.git') !== false) continue;
        
        $content = file_get_contents($path);
        if (strpos($content, $pattern) !== false) {
            echo "Found in: " . $path . "\n";
        }
    }
}
search_dir(__DIR__ . '/..', 'modal-open');
