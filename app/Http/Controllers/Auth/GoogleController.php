<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        $googleClientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');
        
        if (empty($googleClientId) || empty($redirectUri)) {
            return redirect('/login')->withErrors(['email' => 'Google authentication is not configured.']);
        }
        
        $scope = 'email profile';
        $authUrl = 'https://accounts.google.com/o/oauth2/auth?';
        $params = [
            'client_id' => $googleClientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'select_account'
        ];
        
        return redirect($authUrl . http_build_query($params));
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->get('code');
            
            if (!$code) {
                return redirect('/login')->withErrors(['email' => 'Google authentication failed.']);
            }
            
            // Get access token
            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri' => config('services.google.redirect'),
                    'grant_type' => 'authorization_code',
                    'code' => $code
                ]
            ]);
            
            $tokenData = json_decode($response->getBody(), true);
            $accessToken = $tokenData['access_token'];
            
            // Get user info
            $userResponse = $client->get('https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken);
            $googleUser = json_decode($userResponse->getBody(), true);
            
            // Check if user already exists
            $user = User::where('email', $googleUser['email'])->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
            } else {
                // User doesn't exist, create new user
                $user = User::create([
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'username' => $googleUser['email'], // Use email as username
                    'password' => Hash::make(uniqid()), // Generate random password
                    'role' => 'freelancer', // Default role for Google users
                    'custom_id' => 'FL' . rand(100, 999), // Freelancer ID
                ]);
                
                Auth::login($user);
            }
            
            // Redirect based on user role
            $role = $user->role;
            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'notaris':
                    return redirect()->route('notaris.dashboard');
                case 'staff':
                    return redirect()->route('staff.dashboard');
                case 'freelancer':
                    return redirect()->route('freelancer.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
            
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Unable to login using Google. Please try again.']);
        }
    }
}