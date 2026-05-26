<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\NotarisCase;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $cases  = NotarisCase::with('payment')->get();
        $totalCases   = $cases->count();
        $ptCases      = $cases->where('type', 'PT')->count();
        $cvCases      = $cases->where('type', 'CV')->count();
        $pribadiCases = $cases->where('type', 'Pribadi')->count();
        $totalClients = \App\Models\Client::count();

        $selesai  = $cases->where('status', 'selesai')->count();
        $proses   = $cases->where('status', 'proses')->count();
        $tertunda = $cases->where('status', 'tertunda')->count();

        // Cases per year (last 5 years)
        $currentYear = now()->year;
        $yearlyData  = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearlyData[$y] = NotarisCase::whereYear('created_at', $y)->count();
        }

        $activityFeed = $this->buildDashboardActivityFeed();

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
            'yearlyData', 'activityFeed', 'pwdResetNotification',
            'monthlyData', 'monthlyLabels', 'totalUsers'
        ));
    }

    /**
     * Unified feed: audit trail (cases, users, staff, …) + payment status changes.
     */
    private function buildDashboardActivityFeed(int $limit = 14): Collection
    {
        $audits = AuditTrail::with('user')->latest()->take(40)->get();

        $caseIds = $audits->where('table_name', 'cases')->pluck('record_id')->unique()->filter()->values();
        $casesById = NotarisCase::with('payment')
            ->whereIn('id_kasus', $caseIds)
            ->get()
            ->keyBy('id_kasus');

        $items = collect();
        foreach ($audits as $audit) {
            $row = $this->mapAuditToActivity($audit, $casesById);
            if ($row) {
                $items->push($row);
            }
        }

        $payHist = PaymentHistory::with(['payment.case', 'changer'])->latest()->take(25)->get();
        foreach ($payHist as $h) {
            $items->push($this->mapPaymentHistoryToActivity($h));
        }

        return $items
            ->sortByDesc(fn (array $r) => $r['at'] instanceof Carbon ? $r['at']->timestamp : 0)
            ->values()
            ->take($limit);
    }

    private function formatActivityTime(Carbon $at, string $who): string
    {
        $local = $at->copy()->timezone(config('app.timezone', 'Asia/Jakarta'))->locale('id');

        return 'Oleh ' . $who . ' · ' . $local->translatedFormat('d M Y, H:i') . ' WIB';
    }

    private function paymentStatusLabel(?string $s): string
    {
        return match ($s) {
            'lunas' => 'Lunas',
            'sebagian' => 'Sebagian',
            'belum' => 'Belum bayar',
            default => $s ? ucfirst(str_replace('_', ' ', $s)) : '—',
        };
    }

    private function paymentStatusStyle(?string $s): array
    {
        return match ($s) {
            'lunas' => ['bg' => '#dcfce7', 'color' => '#166534'],
            'sebagian' => ['bg' => '#fef3c7', 'color' => '#92400e'],
            'belum' => ['bg' => '#f3f4f6', 'color' => '#4b5563'],
            default => ['bg' => '#e5e7eb', 'color' => '#374151'],
        };
    }

    private function mapPaymentHistoryToActivity(PaymentHistory $h): array
    {
        $case = $h->payment?->case;
        $client = $case?->client_name ?? 'Klien';
        $caseName = $case?->case_name ?? 'Kasus';
        $who = $h->changer?->name ?? 'Sistem';
        $from = $this->paymentStatusLabel($h->from_status);
        $to = $this->paymentStatusLabel($h->to_status);
        $toStyle = $this->paymentStatusStyle($h->to_status);

        return [
            'at' => $h->created_at,
            'source' => 'payment',
            'icon' => 'payment',
            'title' => 'Status pembayaran diubah',
            'detail' => $client . ' · ' . $caseName . ' — ' . $from . ' → ' . $to,
            'meta' => $this->formatActivityTime($h->created_at, $who),
            'badges' => [
                ['text' => 'Pembayaran', 'bg' => '#fff7ed', 'color' => '#c2410c'],
                array_merge(['text' => $to], $toStyle),
            ],
        ];
    }

    private function mapAuditToActivity(AuditTrail $a, Collection $casesById): ?array
    {
        $who = $a->user?->name ?? 'Sistem';
        $nv = $a->new_value ?? [];
        $ov = $a->old_value ?? [];

        $badges = [];
        $caseStatus = null;
        if ($a->table_name === 'cases') {
            $caseRow = $casesById->get($a->record_id);

            if ($a->action === 'status_changed') {
                $oldS = $ov['status'] ?? null;
                $newS = $nv['status'] ?? null;
                $title = 'Status kasus diubah (sistem mencatat)';
                $detail = ($caseRow?->client_name ?? 'Klien') . ' · ' . ($caseRow?->case_name ?? 'Kasus');
                if ($oldS !== null && $newS !== null) {
                    $detail .= ' — ' . ucfirst((string) $oldS) . ' → ' . ucfirst((string) $newS);
                }
                $caseStatus = $newS;
            } elseif ($a->action === 'created') {
                $title = 'Kasus baru dicatat';
                $detail = ($nv['client_name'] ?? 'Klien') . ' · ' . ($nv['case_name'] ?? '—') . ' (' . ($nv['type'] ?? '?') . ')';
            } elseif ($a->action === 'updated') {
                $title = 'Data kasus diperbarui';
                $detail = ($nv['client_name'] ?? 'Klien') . ' · ' . ($nv['case_name'] ?? 'Kasus');
            } elseif ($a->action === 'deleted') {
                $title = 'Kasus dihapus';
                $detail = ($ov['client_name'] ?? $nv['client_name'] ?? 'Kasus') . ' · ' . ($ov['case_name'] ?? $nv['case_name'] ?? '');
            } elseif ($a->action === 'document_uploaded') {
                $title = 'Dokumen diunggah ke kasus';
                $fn = $nv['filename'] ?? 'berkas';
                $detail = 'Berkas: ' . $fn . ' · ID kasus ' . $a->record_id;
            } elseif ($a->action === 'document_deleted') {
                $title = 'Dokumen dihapus dari kasus';
                $fn = $ov['filename'] ?? 'berkas';
                $detail = 'Berkas: ' . $fn . ' · ID kasus ' . $a->record_id;
            } else {
                $title = 'Aktivitas kasus: ' . str_replace('_', ' ', $a->action);
                $detail = 'ID ' . $a->record_id;
            }

            $colors = ['selesai' => '#22c55e', 'proses' => '#f59e0b', 'tertunda' => '#ef4444'];
            if ($caseStatus) {
                $c = $colors[$caseStatus] ?? '#6b7280';
                $badges[] = ['text' => 'Kasus: ' . ucfirst((string) $caseStatus), 'bg' => $c . '22', 'color' => $c];
            } elseif (isset($nv['status'])) {
                $st = $nv['status'];
                $c = $colors[$st] ?? '#6b7280';
                $badges[] = ['text' => 'Kasus: ' . ucfirst((string) $st), 'bg' => $c . '22', 'color' => $c];
            }

            if ($caseRow?->payment) {
                $pst = $caseRow->payment->status;
                $ps = $this->paymentStatusStyle($pst);
                $badges[] = array_merge(['text' => 'Bayar: ' . $this->paymentStatusLabel($pst)], $ps);
            }

            return [
                'at' => $a->created_at,
                'source' => 'case',
                'icon' => 'case',
                'title' => $title,
                'detail' => $detail,
                'meta' => $this->formatActivityTime($a->created_at, $who),
                'badges' => $badges,
            ];
        }

        if ($a->table_name === 'users') {
            $label = match ($a->action) {
                'created' => 'Pengguna ditambahkan',
                'updated' => 'Data pengguna diubah',
                'deleted' => 'Pengguna dihapus',
                'deactivated' => 'Pengguna dinonaktifkan',
                'restored' => 'Pengguna diaktifkan kembali',
                'password_changed' => 'Kata sandi pengguna diubah',
                'profile_updated' => 'Profil pengguna diperbarui',
                default => 'Aktivitas akun: ' . str_replace('_', ' ', $a->action),
            };
            $name = $nv['name'] ?? $ov['name'] ?? $a->record_id;

            return [
                'at' => $a->created_at,
                'source' => 'user',
                'icon' => 'user',
                'title' => $label,
                'detail' => (string) $name,
                'meta' => $this->formatActivityTime($a->created_at, $who),
                'badges' => [['text' => 'Akun', 'bg' => '#e0e7ff', 'color' => '#4338ca']],
            ];
        }

        if ($a->table_name === 'staffs') {
            $label = match ($a->action) {
                'created' => 'Staff baru ditambahkan',
                'updated' => 'Data staff diperbarui',
                'deleted' => 'Staff dihapus',
                default => 'Staff: ' . $a->action,
            };
            $name = $nv['name'] ?? $ov['name'] ?? $a->record_id;

            return [
                'at' => $a->created_at,
                'source' => 'staff',
                'icon' => 'staff',
                'title' => $label,
                'detail' => (string) $name,
                'meta' => $this->formatActivityTime($a->created_at, $who),
                'badges' => [['text' => 'Staff', 'bg' => '#fce7f3', 'color' => '#9d174d']],
            ];
        }

        return null;
    }

    public function reports(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admin tidak memiliki akses ke Report Archive.');
        }

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
