<?php

namespace App\Http\Controllers;

use App\Models\CaseNote;
use App\Models\AuditTrail;
use App\Models\NotarisCase;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CaseDocument;

class CaseController extends Controller
{
    // ── Status permissions per role ─────────────────────────────────────────
    private const ROLE_STATUS_LIMIT = [
        'freelancer' => ['proses'],                        // freelancer: only proses
        'staff'      => ['proses', 'tertunda', 'selesai'], // staff: all statuses
        'admin'      => ['proses', 'tertunda', 'selesai'],
        'notaris'    => ['proses', 'tertunda', 'selesai'],
    ];

    public function index(Request $request)
    {
        $query = NotarisCase::with(['payment', 'creator'])->latest();

        // Admin can see everything, others might be filtered
        if (Auth::user()->role === 'freelancer') {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month'))  $query->whereMonth('created_at', $request->month);
        if ($request->filled('year'))   $query->whereYear('created_at', $request->year);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('client_name', 'like', "%$s%")
                                      ->orWhere('case_name', 'like', "%$s%"));
        }

        $cases = $query->get();
        $clients = \App\Models\Client::all();
        return view('cases.index', compact('cases', 'clients'));
    }

    public function show($id)
    {
        return $this->edit($id);
    }



    public function calendar()
    {
        $cases = NotarisCase::select('id_kasus', 'client_name', 'case_name', 'status', 'deadline', 'type')->get();
        return view('cases.calendar', compact('cases'));
    }


    public function store(Request $request)
    {
        // Sanitize nominal_bayar: strip "Rp." and dots
        if ($request->has('nominal_bayar')) {
            $request->merge(['nominal_bayar' => preg_replace('/\D/', '', $request->nominal_bayar)]);
        }

        $validated = $request->validate([
            'id_klien'      => 'nullable|string|exists:clients,id_klien',
            'client_name'   => 'required|string|max:255',
            'phone'         => 'nullable|string|max:30',
            'address'       => 'nullable|string',
            'birth_date'    => 'nullable|date',
            'case_name'     => 'required|string|max:255',
            'type'          => 'required|in:PT,CV,Pribadi',
            'deadline'      => 'required|date',
            'nominal_bayar' => 'nullable|numeric|min:0',
        ]);

        $validated['status']     = 'proses';
        $validated['created_by'] = Auth::id();

        $fileFields = ['file_ktp', 'file_npwp', 'file_kk', 'file_surat_tanah', 'file_surat_perintah', 'file_buku_nikah'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('case-files', 'public');
            }
        }

        $case = NotarisCase::create($validated);

        // Save formatted amount in payment
        $amountFormatted = $request->nominal_bayar ? 'Rp. ' . number_format($request->nominal_bayar, 0, ',', '.') : null;
        Payment::create(['id_kasus' => $case->id_kasus, 'amount' => $amountFormatted, 'status' => 'belum']);

        AuditTrail::log('cases', $case->id_kasus, 'created', null, $case->toArray());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'case' => $case->load('payment')]);
        }
        return redirect()->route('cases.index')->with('success', "Kasus \"{$case->client_name}\" berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $case = NotarisCase::with(['payment', 'documents', 'caseNotes' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('id_kasus', $id)->firstOrFail();
        return view('cases.edit', compact('case'));
    }

    public function update(Request $request, $id)
    {
        $case = NotarisCase::where('id_kasus', $id)->firstOrFail();

        // Freelancer cannot edit cases
        if (Auth::user()->role === 'freelancer') {
            abort(403, 'Freelancer tidak dapat mengedit kasus.');
        }

        // Sanitize nominal_bayar: strip "Rp." and dots
        if ($request->has('nominal_bayar')) {
            $request->merge(['nominal_bayar' => preg_replace('/\D/', '', $request->nominal_bayar)]);
        }

        $validated = $request->validate([
            'id_klien'      => 'nullable|string|exists:clients,id_klien',
            'client_name'   => 'required|string|max:255',
            'phone'         => 'nullable|string|max:30',
            'address'       => 'nullable|string',
            'birth_date'    => 'nullable|date',
            'case_name'     => 'required|string|max:255',
            'type'          => 'required|in:PT,CV,Pribadi',
            'deadline'      => 'required|date',
            'nominal_bayar' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:proses,tertunda,selesai',
            'progress_note' => 'nullable|string|max:1000',
        ]);

        $fileFields = ['file_ktp', 'file_npwp', 'file_kk', 'file_surat_tanah', 'file_surat_perintah', 'file_buku_nikah'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($case->$field) Storage::disk('public')->delete($case->$field);
                $validated[$field] = $request->file($field)->store('case-files', 'public');
            }
        }

        $oldStatus = $case->status;
        $oldNote   = $case->progress_note;
        $old       = $case->toArray();

        $case->update($validated);

        // Synchronize payment amount if nominal_bayar changed
        if ($request->has('nominal_bayar')) {
            $payment = Payment::where('id_kasus', $case->id_kasus)->first();
            if ($payment) {
                $amountFormatted = $request->nominal_bayar ? 'Rp. ' . number_format($request->nominal_bayar, 0, ',', '.') : null;
                $payment->update(['amount' => $amountFormatted]);
            }
        }

        // Log to timeline if status or note changed
        if ($case->status !== $oldStatus || $case->progress_note !== $oldNote) {
            CaseNote::create([
                'id_kasus' => $case->id_kasus,
                'user_id'  => Auth::id(),
                'status'   => $case->status,
                'note'     => $case->progress_note,
            ]);
        }

        AuditTrail::log('cases', $case->id_kasus, 'updated', $old, $case->fresh()->toArray());

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('cases.index')->with('success', "Kasus \"{$case->client_name}\" berhasil diperbarui.");
    }

    public function updateStatus(Request $request, $id)
    {
        $case    = NotarisCase::where('id_kasus', $id)->firstOrFail();
        $role    = Auth::user()->role;
        $allowed = self::ROLE_STATUS_LIMIT[$role] ?? ['proses', 'tertunda', 'selesai'];
        $new     = $request->input('status');

        if (!in_array($new, ['proses', 'tertunda', 'selesai'])) {
            return response()->json(['error' => 'Status tidak valid.'], 422);
        }
        if (!in_array($new, $allowed)) {
            return response()->json(['error' => "Role {$role} tidak dapat mengatur status '{$new}'."], 403);
        }

        $old = $case->status;
        $case->update(['status' => $new]);

        // Log to timeline
        CaseNote::create([
            'id_kasus' => $case->id_kasus,
            'user_id'  => Auth::id(),
            'status'   => $new,
            'note'     => $case->progress_note, // Keep current note
        ]);

        AuditTrail::log('cases', $case->id_kasus, 'status_changed', ['status' => $old], ['status' => $new]);

        return response()->json(['success' => true, 'old' => $old, 'new' => $new]);
    }

    public function updateNote(Request $request, $id)
    {
        $case = NotarisCase::where('id_kasus', $id)->firstOrFail();
        $newNote = $request->input('note', '');
        $case->update(['progress_note' => $newNote]);

        // Log to timeline
        CaseNote::create([
            'id_kasus' => $case->id_kasus,
            'user_id'  => Auth::id(),
            'status'   => $case->status,
            'note'     => $newNote,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, $id)
    {
        $case = NotarisCase::where('id_kasus', $id)->firstOrFail();
        $name = $case->client_name;
        AuditTrail::log('cases', $case->id_kasus, 'deleted', $case->toArray(), null);
        $case->delete();

        if ($request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('cases.index')->with('success', "Kasus \"{$name}\" berhasil dihapus.");
    }

    // ── Document Management ─────────────────────────────────────────────────
    
    public function uploadDocument(Request $request, $id)
    {
        $case = NotarisCase::where('id_kasus', $id)->firstOrFail();
        
        // Prevent freelancers from uploading documents
        if (Auth::user()->role === 'freelancer') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120' // 5MB limit
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = $file->getClientOriginalName();
            $path = $file->store('case-documents', 'public');

            $doc = CaseDocument::create([
                'id_kasus' => $case->id_kasus,
                'filename' => $filename,
                'filepath' => $path,
                'uploaded_by' => Auth::id()
            ]);

            AuditTrail::log('cases', $case->id_kasus, 'document_uploaded', null, ['filename' => $filename]);

            return response()->json([
                'success' => true, 
                'document' => [
                    'id' => $doc->id_dok,
                    'filename' => $doc->filename,
                    'url' => asset('storage/' . $doc->filepath),
                    'created_at' => $doc->created_at->format('d/m/Y H:i')
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah dokumen.']);
    }

    public function deleteDocument($id)
    {
        $doc = CaseDocument::where('id_dok', $id)->firstOrFail();
        
        // Prevent freelancers from deleting documents
        if (Auth::user()->role === 'freelancer') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $id_kasus = $doc->id_kasus;
        $filename = $doc->filename;

        Storage::disk('public')->delete($doc->filepath);
        $doc->delete();

        AuditTrail::log('cases', $id_kasus, 'document_deleted', ['filename' => $filename], null);

        return response()->json(['success' => true]);
    }
}

