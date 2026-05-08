<?php
// Simple test script to verify Google login implementation
require_once 'vendor/autoload.php';

// Test environment variables
$googleClientId = getenv('GOOGLE_CLIENT_ID');
$googleClientSecret = getenv('GOOGLE_CLIENT_SECRET');
$googleRedirectUri = getenv('GOOGLE_REDIRECT_URI');

echo "Google Login Configuration Test\n";
echo "================================\n\n";

echo "GOOGLE_CLIENT_ID: " . ($googleClientId ? "SET" : "NOT SET") . "\n";
echo "GOOGLE_CLIENT_SECRET: " . ($googleClientSecret ? "SET" : "NOT SET") . "\n";
echo "GOOGLE_REDIRECT_URI: " . ($googleRedirectUri ?: "NOT SET") . "\n\n";

if ($googleClientId && $googleClientSecret && $googleRedirectUri) {
    echo "✅ All Google OAuth credentials are configured!\n";
    echo "You can now test Google login at: http://localhost:8000/login\n\n";
    
    echo "Test the implementation:\n";
    echo "1. Visit http://localhost:8000/login\n";
    echo "2. Look for the 'Masuk dengan Google' button\n";
    echo "3. Click the button to initiate Google login\n";
    echo "4. You should be redirected to Google's OAuth page\n";
} else {
    echo "❌ Google OAuth credentials are not fully configured.\n";
    echo "Please follow the setup instructions in GOOGLE_LOGIN_SETUP.md\n\n";
    
    echo "Missing configurations:\n";
    if (!$googleClientId) echo "- GOOGLE_CLIENT_ID\n";
    if (!$googleClientSecret) echo "- GOOGLE_CLIENT_SECRET\n";
    if (!$googleRedirectUri) echo "- GOOGLE_REDIRECT_URI\n";
}