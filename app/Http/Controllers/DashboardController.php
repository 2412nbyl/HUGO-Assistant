<?php

namespace App\Http\Controllers;

use App\Models\NotarisCase;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $cases  = NotarisCase::with('payment')->get();
        $totalCases   = $cases->count();
        $ptCases      = $cases->where('type', 'PT')->count();
        $cvCases      = $cases->where('type', 'CV')->count();
        $pribadiCases = $cases->where('type', 'Pribadi')->count();
        $totalClients = $cases->unique('client_name')->count();

        $selesai  = $cases->where('status', 'selesai')->count();
        $proses   = $cases->where('status', 'proses')->count();
        $tertunda = $cases->where('status', 'tertunda')->count();

        // Cases per year (last 5 years)
        $currentYear = now()->year;
        $yearlyData  = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearlyData[$y] = NotarisCase::whereYear('created_at', $y)->count();
        }

        // Recent cases
        $recentCases = NotarisCase::with('payment')->latest()->take(5)->get();

        // Check for password reset notification (admin chat)
        $pwdResetNotification = null;
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'notaris') {
            $pwdResetNotification = \Illuminate\Support\Facades\Cache::pull('pwd_reset_notify');
        }

        // Monthly cases count (last 12 months) for Bar chart
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthlyLabels[] = $d->format('M Y');
            $monthlyData[]   = NotarisCase::whereYear('created_at', $d->year)
                                          ->whereMonth('created_at', $d->month)
                                          ->count();
        }

        // Users per role for reference
        $totalUsers = User::count();

        return view('dashboard', compact(
            'totalCases', 'ptCases', 'cvCases', 'pribadiCases',
            'totalClients', 'selesai', 'proses', 'tertunda',
            'yearlyData', 'recentCases', 'pwdResetNotification',
            'monthlyData', 'monthlyLabels', 'totalUsers'
        ));
    }

    public function reports(Request $request)
    {
        $query = NotarisCase::with('payment')->latest();

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year ?: now()->year);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('client_name','like',"%$s%")->orWhere('case_name','like',"%$s%");
            });
        }

        $cases = $query->get();
        return view('reports.index', compact('cases'));
    }

}
