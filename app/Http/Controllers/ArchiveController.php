<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\NotarisCase;
use App\Models\Client;
use App\Models\CaseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    /**
     * All authenticated roles (admin, notaris, staff, freelancer) can access archives.
     * Also loads finished cases data for the unified Document Archive page.
     */
    public function index(Request $request)
    {
        // Load archives with their linked case documents
        $query = Archive::with(['case.documents'])->latest();

        // Freelancer sees only their own
        if (Auth::user()->role === 'freelancer') {
            $query->whereHas('case', function ($q) {
                $q->where('created_by', Auth::id());
            });
        }

        $searchSupport = $request->input('search_support');
        $searchFinished = $request->input('search_finished');

        // Legacy: handle single 'search' param for backward compat
        if ($request->filled('search') && !$searchSupport) {
            $searchSupport = $request->search;
        }

        if ($searchSupport) {
            $s = $searchSupport;
            $query->where(function ($q) use ($s) {
                $q->where('client_name', 'like', "%{$s}%")
                  ->orWhere('id_arsip', 'like', "%{$s}%")
                  ->orWhere('id_kasus', 'like', "%{$s}%")
                  ->orWhere('folder_location', 'like', "%{$s}%");
            });
        }

        $archives = $query->paginate(15)->withQueryString();

        // Also load standalone case documents (not yet in an archive folder)
        $orphanDocs = CaseDocument::with('case')
            ->whereNull('id_arsip')
            ->latest()
            ->get();

        // ── Finished Cases (for combined tab) ─────────────────────────────
        $finishedQuery = NotarisCase::with(['documents', 'payment'])
            ->where('status', 'selesai')
            ->latest();

        // Freelancer sees only their own
        if (Auth::user()->role === 'freelancer') {
            $finishedQuery->where('created_by', Auth::id());
        }

        if ($searchFinished) {
            $s = $searchFinished;
            $finishedQuery->where(function ($q) use ($s) {
                $q->where('client_name', 'like', "%{$s}%")
                  ->orWhere('case_name', 'like', "%{$s}%")
                  ->orWhere('id_kasus', 'like', "%{$s}%");
            });
        }

        $finishedCases = $finishedQuery->get();

        // Attach folder_location + is_physical to each finished case
        foreach ($finishedCases as $case) {
            $archive = Archive::where('id_kasus', $case->id_kasus)->first();
            if ($archive && !empty($archive->folder_location)) {
                $case->folder_location = $archive->folder_location;
                $case->is_physical = strpos($archive->folder_location, '/') === false
                    && strpos($archive->folder_location, '\\') === false;
            } else {
                $case->folder_location = storage_path('app/public/case-files/' . $case->id_kasus);
                $case->is_physical = false;
            }
            $case->folder_location = str_replace('/', DIRECTORY_SEPARATOR, $case->folder_location);
        }

        // Determine active tab from query param (default: support)
        $activeTab = $request->input('tab', 'support');

        return view('archives.index', compact('archives', 'orphanDocs', 'finishedCases', 'activeTab', 'searchSupport', 'searchFinished'));
    }

    public function create()
    {
        $cases   = NotarisCase::orderBy('client_name')->get();
        $clients = Client::orderBy('name')->get();
        return view('archives.create', compact('cases', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kasus'        => 'required|string|exists:cases,id_kasus',
            'id_klien'        => 'nullable|string',
            'client_name'     => 'required|string|max:255',
            'folder_location' => 'required|string|max:500',
        ]);

        $archive = Archive::create($validated);

        // Link all existing case documents to this archive folder
        CaseDocument::where('id_kasus', $validated['id_kasus'])
            ->whereNull('id_arsip')
            ->update(['id_arsip' => $archive->id_arsip]);

        return redirect()->route('archives.index')->with('success', 'Arsip berhasil dibuat dan dokumen kasus telah ditautkan.');
    }

    public function edit(Archive $archive)
    {
        $cases   = NotarisCase::orderBy('client_name')->get();
        $clients = Client::orderBy('name')->get();
        return view('archives.edit', compact('archive', 'cases', 'clients'));
    }

    public function update(Request $request, Archive $archive)
    {
        $validated = $request->validate([
            'id_kasus'        => 'required|string|exists:cases,id_kasus',
            'id_klien'        => 'nullable|string',
            'client_name'     => 'required|string|max:255',
            'folder_location' => 'required|string|max:500',
        ]);

        $archive->update($validated);

        return redirect()->route('archives.index')->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy(Archive $archive)
    {
        // Unlink documents from this archive (don't delete the files)
        CaseDocument::where('id_arsip', $archive->id_arsip)
            ->update(['id_arsip' => null]);

        $archive->delete();
        return redirect()->route('archives.index')->with('success', 'Arsip berhasil dihapus.');
    }
}
