# Google Login Setup Instructions

## Prerequisites
- Google Account
- Access to Google Cloud Console

## Setup Steps

### 1. Create Google OAuth 2.0 Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Navigate to "APIs & Services" > "Credentials"
4. Click "Create Credentials" > "OAuth client ID"
5. If prompted, configure the OAuth consent screen first:
   - Set "User Type" to "External"
   - Fill in required app information
   - Add your email to test users
6. For "Application type", select "Web application"
7. Add authorized redirect URIs:
   - `http://localhost/auth/google/callback` (for local development)
   - Add your production URL when ready
8. Click "Create"
9. Copy the "Client ID" and "Client Secret"

### 2. Configure Environment Variables

Add the following to your `.env` file:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

### 3. Test the Implementation

1. Start your Laravel development server:
   ```bash
   php artisan serve
   ```

2. Visit `http://localhost:8000/login`

3. You should see a "Masuk dengan Google" button

4. Click the button to initiate Google login

### 4. How It Works

When a user clicks "Masuk dengan Google":
1. They are redirected to Google's OAuth page
2. After authentication, Google redirects back to your callback URL
3. The application exchanges the authorization code for an access token
4. User information is retrieved from Google
5. If the user exists in your database, they are logged in
6. If the user doesn't exist, a new account is created with:
   - Name from Google
   - Email as username
   - Random password (since they'll use Google login)
   - Default role: freelancer
   - Custom ID: FL + random number

### 5. Customization

You can modify the default behavior in `app/Http/Controllers/Auth/GoogleController.php`:
- Change default role for new users
- Modify user creation logic
- Add additional user information from Google
- Customize redirect behavior after login

### 6. Security Notes

- Keep your `GOOGLE_CLIENT_SECRET` secure and never commit it to version control
- Use different credentials for development and production environments
- The implementation uses HTTPS in production (recommended)
- Access tokens are not stored in the database for security

### 7. Troubleshooting

**Common Issues:**
- "Google authentication is not configured" - Check your .env file
- "Redirect URI mismatch" - Make sure your redirect URI matches exactly in Google Cloud Console
- "Invalid client" - Verify your Client ID is correct
- Database connection errors - Ensure your database is configured properly

**Debugging:**
- Check Laravel logs: `storage/logs/laravel.log`
- Enable debug mode in `.env`: `APP_DEBUG=true`
- Check browser developer tools for any JavaScript errors