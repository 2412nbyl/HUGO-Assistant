<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * OAuth redirect_uri must exactly match the host used to start login.
     * Local and ngrok URLs are both registered in Google Cloud Console.
     */
    private function googleRedirectUrl(Request $request): string
    {
        $ngrokFull = trim((string) config('services.google.redirect_ngrok', ''));
        if ($ngrokFull !== '') {
            $ngrokHost = parse_url($ngrokFull, PHP_URL_HOST);
            if ($ngrokHost && strcasecmp($request->getHost(), $ngrokHost) === 0) {
                return $ngrokFull;
            }
        }

        $local = trim((string) config('services.google.redirect', ''));
        if ($local !== '') {
            return $local;
        }

        return url('/auth/google/callback');
    }

    public function redirectToGoogle(Request $request)
    {
        $redirectUrl = $this->googleRedirectUrl($request);

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $redirectUrl = $this->googleRedirectUrl($request);
            $user = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->stateless()
                ->user();
            
            $finduser = User::where('google_id', $user->id)
                            ->orWhere('email', $user->email)
                            ->first();

            if($finduser){
                // Update google_id if not set (linking)
                if (!$finduser->google_id) {
                    $finduser->update([
                        'google_id' => $user->id,
                        'google_token' => $user->token,
                        'google_refresh_token' => $user->refreshToken,
                        'avatar_url' => $user->avatar,
                    ]);
                } else {
                    $finduser->update([
                        'google_token' => $user->token,
                        'google_refresh_token' => $user->refreshToken,
                        'avatar_url' => $user->avatar,
                    ]);
                }

                Auth::login($finduser);
                session(['just_logged_in' => true]);

                return redirect()->intended(route('dashboard'));
            } else {
                // Random bcrypt hash — Google users sign in via OAuth only, but the column must hold a valid hash.
                $randomPassword = bin2hex(random_bytes(24));

                $baseUsername = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $user->name));
                if (strlen($baseUsername) < 2) {
                    $baseUsername = 'user';
                }
                $baseUsername = substr($baseUsername, 0, 32);
                $username = $baseUsername;
                $n = 0;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . rand(100, 99999);
                    if (++$n > 30) {
                        $username = 'g_' . preg_replace('/\W/', '', (string) $user->id) . '_' . rand(100, 999);
                        break;
                    }
                }

                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'google_token' => $user->token,
                    'google_refresh_token' => $user->refreshToken,
                    'avatar_url' => $user->avatar,
                    'password' => Hash::make($randomPassword),
                    'role' => 'freelancer',
                    'username' => $username,
                    'is_active' => true,
                ]);

                Auth::login($newUser);
                session(['just_logged_in' => true]);

                return redirect()->intended(route('dashboard'));
            }

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth login failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google. Silakan coba kembali atau gunakan kata sandi Anda.');
        }
    }
}
