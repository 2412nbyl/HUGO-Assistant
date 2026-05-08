<?php
/**
 * One-time logo copy helper — run this once at:
 * http://localhost/HUGO-Assistant/public/favicon.png   (it's now a PHP fallback)
 * 
 * Actually, this file just outputs the logo image directly if favicon.png is missing.
 * But the proper fix is to just save the logo as a PNG.
 */

// If favicon.png already exists, serve it
$pngPath = __DIR__ . '/favicon.png';
if (file_exists($pngPath)) {
    header('Content-Type: image/png');
    readfile($pngPath);
    exit;
}

// Fallback: Generate a simple "H" logo as an SVG converted to PNG
// This is a last-resort fallback
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">';
echo '<rect width="512" height="512" rx="80" fill="#111827"/>';
echo '<text x="256" y="360" text-anchor="middle" font-family="Arial,sans-serif" font-size="280" font-weight="900" fill="#dc2626">H</text>';
echo '</svg>';
