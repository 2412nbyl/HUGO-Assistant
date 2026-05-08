<?php

namespace App\Http\Controllers;

use App\Models\NotarisCase;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * Export cases in the requested format.
     * Supported: excel (xlsx), csv, pdf
     */
    public function exportCases(Request $request, string $format)
    {
        $query = NotarisCase::with(['payment', 'creator'])->latest();

        // Apply the same filters as the cases index
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('year'))   $query->whereYear('created_at', $request->year);

        $cases = $query->get();

        return match($format) {
            'csv'   => $this->casesToCsv($cases),
            'pdf'   => $this->casesToPdf($cases),
            default => $this->casesToCsv($cases), // fallback
        };
    }

    /**
     * Export payments in the requested format.
     */
    public function exportPayments(Request $request, string $format)
    {
        $payments = Payment::with(['case', 'histories.changer'])->latest()->get();

        return match($format) {
            'csv'   => $this->paymentsToCsv($payments),
            'pdf'   => $this->paymentsToPdf($payments),
            default => $this->paymentsToCsv($payments),
        };
    }

    // ── Cases CSV ──────────────────────────────────────────────────────────────

    private function casesToCsv($cases)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="kasus-' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($cases) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No', 'Nama Klien', 'Nama Kasus', 'Jenis', 'Status', 'Deadline', 'Nominal', 'Dibuat Oleh', 'Tgl Dibuat']);
            foreach ($cases as $i => $c) {
                fputcsv($out, [
                    $i + 1,
                    $c->client_name,
                    $c->case_name,
                    $c->type,
                    $c->status,
                    $c->deadline?->format('d-m-Y'),
                    $c->nominal_bayar ? 'Rp ' . number_format($c->nominal_bayar, 0, ',', '.') : '-',
                    $c->creator?->name ?? '-',
                    $c->created_at->format('d-m-Y'),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Cases PDF (HTML-based, no external package required) ──────────────────

    private function casesToPdf($cases)
    {
        $html = view('exports.cases-pdf', compact('cases'))->render();
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8');
        // Note: For true PDF, install barryvdh/laravel-dompdf later.
        // For now, opens in browser as a print-ready HTML — user can Ctrl+P → Save as PDF
    }

    // ── Payments CSV ───────────────────────────────────────────────────────────

    private function paymentsToCsv($payments)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pembayaran-' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($payments) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No', 'Klien', 'Nama Kasus', 'Jumlah', 'Status', 'Tgl Update']);
            foreach ($payments as $i => $p) {
                fputcsv($out, [
                    $i + 1,
                    $p->case?->client_name ?? '-',
                    $p->case?->case_name ?? '-',
                    $p->amount ?? '-',
                    $p->status,
                    $p->updated_at->format('d-m-Y'),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function paymentsToPdf($payments)
    {
        $html = view('exports.payments-pdf', compact('payments'))->render();
        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
