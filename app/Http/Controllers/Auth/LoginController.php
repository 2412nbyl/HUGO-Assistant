<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->username);
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        $user = User::query()
            ->when($isEmail, function ($q) use ($login) {
                $q->whereRaw('LOWER(email) = ?', [strtolower($login)]);
            }, function ($q) use ($login) {
                $q->where('username', $login);
            })
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['username' => 'Username/Email atau password salah.'])
                ->withInput();
        }

        if ($user->trashed() || $user->is_active === false) {
            return back()
                ->withErrors(['username' => 'Akun nonaktif. Hubungi administrator.'])
                ->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['username' => 'Username/Email atau password salah.'])
                ->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->flash('just_logged_in', true);

        return redirect()->route('dashboard');
    }
}
