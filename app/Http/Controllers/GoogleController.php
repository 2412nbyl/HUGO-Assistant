<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
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
                return redirect()->intended('dashboard');
            }else{
                // If user doesn't exist, create a new one as 'freelancer' by default
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'google_token' => $user->token,
                    'google_refresh_token' => $user->refreshToken,
                    'avatar_url' => $user->avatar,
                    'password' => encrypt('hugo-google-auth-random-pass-' . rand(1000, 9999)),
                    'role' => 'freelancer',
                    'username' => strtolower(str_replace(' ', '', $user->name)) . rand(100, 999),
                    'is_active' => true,
                ]);

                Auth::login($newUser);
                session(['just_logged_in' => true]);
                return redirect()->intended('dashboard');
            }

        } catch (Exception $e) {
            return redirect('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}
