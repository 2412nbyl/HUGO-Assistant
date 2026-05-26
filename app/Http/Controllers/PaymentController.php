<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\NotarisCase;
use App\Models\CaseNote;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['case', 'histories.changer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('case', function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('case_name', 'like', "%{$search}%");
            });
        }

        $payments = $query->get();
        return view('payment.index', compact('payments'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:lunas,sebagian,belum',
            'note'             => 'nullable|string|max:500',
            'nominal_sebagian' => 'nullable|string',
        ]);

        $payment = Payment::with('case')->findOrFail($id);
        $oldStatus = $payment->status;
        $newStatus = $request->status;

        $nominalSebagian = 0;
        if ($newStatus === 'sebagian' && $request->filled('nominal_sebagian')) {
            $nominalSebagian = (int) preg_replace('/\D/', '', $request->nominal_sebagian);
        }

        // Handle case nominal reduction if status is 'sebagian'
        if ($newStatus === 'sebagian' && $payment->case && $nominalSebagian > 0) {
            $case = $payment->case;
            $oldNominal = $case->nominal_bayar;
            $newNominal = max(0, $oldNominal - $nominalSebagian);

            // Update Case nominal
            $case->update(['nominal_bayar' => $newNominal]);

            // Update Payment Amount
            $amountFormatted = 'Rp. ' . number_format($newNominal, 0, ',', '.');
            $payment->update(['amount' => $amountFormatted]);

            // Log details in note
            $noteWithNominal = "Bayar Sebagian: Rp. " . number_format($nominalSebagian, 0, ',', '.') . " (Sisa: " . $amountFormatted . ")";
            if ($request->note) {
                $noteWithNominal .= " — " . $request->note;
            }
            $request->merge(['note' => $noteWithNominal]);
        }

        if ($oldStatus !== $newStatus || $nominalSebagian > 0) {
            PaymentHistory::create([
                'payment_id'  => $payment->id_transaksi,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'amount_paid' => $nominalSebagian,
                'note'        => $request->note,
                'changed_by'  => Auth::id(),
            ]);

            $payment->update(['status' => $newStatus]);

            // Sync Case Status
            if ($payment->case) {
                $case = $payment->case;
                $oldCaseStatus = $case->status;
                if ($newStatus === 'lunas' && $oldCaseStatus !== 'selesai') {
                    $case->update(['status' => 'selesai']);
                    CaseNote::create([
                        'id_kasus' => $case->id_kasus,
                        'user_id'  => Auth::id(),
                        'status'   => 'selesai',
                        'note'     => 'Otomatis: status pembayaran diubah menjadi lunas',
                    ]);
                    AuditTrail::log('cases', $case->id_kasus, 'status_changed', ['status' => $oldCaseStatus], ['status' => 'selesai']);
                } elseif (in_array($newStatus, ['belum', 'sebagian']) && $oldCaseStatus === 'selesai') {
                    $case->update(['status' => 'proses']);
                    CaseNote::create([
                        'id_kasus' => $case->id_kasus,
                        'user_id'  => Auth::id(),
                        'status'   => 'proses',
                        'note'     => 'Otomatis: status pembayaran diubah menjadi ' . $newStatus,
                    ]);
                    AuditTrail::log('cases', $case->id_kasus, 'status_changed', ['status' => $oldCaseStatus], ['status' => 'proses']);
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'payment' => $payment->load('histories.changer')]);
        }

        return redirect()->route('payment.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
