<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('role:admin,notaris')     // allow only admin & notaris
     *   ->middleware('role:!freelancer')        // deny freelancer
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();
        if (!$user) return redirect('/login');

        foreach ($roles as $role) {
            // Deny pattern: "!rolename"
            if (str_starts_with($role, '!')) {
                $denied = substr($role, 1);
                if ($user->role === $denied) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'Akses ditolak.'], 403);
                    }
                    // If no referrer, redirect to dashboard
                    if ($request->header('referer')) {
                        return back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
                    }
                    return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
                }
            }
        }

        // Allow pattern: only specific roles
        $allowOnly = array_filter($roles, fn($r) => !str_starts_with($r, '!'));
        if (!empty($allowOnly) && !in_array($user->role, $allowOnly)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Akses ditolak.'], 403);
            }
            // If no referrer or recursive redirect, go to dashboard
            if ($request->header('referer')) {
                return back()->with('error', 'Anda tidak memiliki akses ke fitur ini.');
            }
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        return $next($request);
    }
}
