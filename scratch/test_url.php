<?php
$urls = [
    'http://localhost/HUGO-Assistant/public/storage/case-files/dummy_ktp.pdf',
    'http://hugo-assistant.test/storage/case-files/dummy_ktp.pdf',
    'http://127.0.0.1/HUGO-Assistant/public/storage/case-files/dummy_ktp.pdf'
];

foreach ($urls as $url) {
    echo "Requesting URL: " . $url . "\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    echo "HTTP Code: " . $httpCode . ($err ? " (Error: $err)" : "") . "\n\n";
}
