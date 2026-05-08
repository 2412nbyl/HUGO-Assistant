<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\NotarisCase;
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
            'status' => 'required|in:lunas,sebagian,belum',
            'note'   => 'nullable|string|max:500',
        ]);

        $payment = Payment::with('case')->findOrFail($id);
        $oldStatus = $payment->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            PaymentHistory::create([
                'payment_id'  => $payment->id,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'note'        => $request->note,
                'changed_by'  => Auth::id(),
            ]);

            $payment->update(['status' => $newStatus]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'payment' => $payment->load('histories.changer')]);
        }

        return redirect()->route('payment.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
