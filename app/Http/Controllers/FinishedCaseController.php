<?php

namespace App\Http\Controllers;

use App\Models\NotarisCase;
use App\Models\Archive;
use App\Models\CaseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinishedCaseController extends Controller
{
    /**
     * Show a listing of completed cases with their documents.
     */
    public function index(Request $request)
    {
        $query = NotarisCase::with(['documents', 'payment'])
            ->where('status', 'selesai')
            ->latest();

        // Freelancer can only see their own cases
        if (Auth::user()->role === 'freelancer') {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('client_name', 'like', "%{$s}%")
                  ->orWhere('case_name', 'like', "%{$s}%")
                  ->orWhere('id_kasus', 'like', "%{$s}%");
            });
        }

        $cases = $query->get();

        $isLocal = request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' || env('APP_ENV') === 'local';

        // Attach folder location and list files for each case
        foreach ($cases as $case) {
            $archive = Archive::where('id_kasus', $case->id_kasus)->first();
            
            // Build the local folder path
            if ($archive && !empty($archive->folder_location)) {
                $case->folder_location = $archive->folder_location;
                $case->is_physical = strpos($archive->folder_location, '/') === false && strpos($archive->folder_location, '\\') === false;
            } else {
                if ($isLocal) {
                    $case->folder_location = storage_path('app/public/case-files/' . $case->id_kasus);
                } else {
                    $case->folder_location = url('storage/case-files/' . $case->id_kasus);
                }
                $case->is_physical = false;
            }

            // Standardize/fix paths to windows formatting only on local
            if ($isLocal) {
                $case->folder_location = str_replace('/', DIRECTORY_SEPARATOR, $case->folder_location);
            }
        }

        return view('finished_cases.index', compact('cases'));
    }

    /**
     * Open case folder in Windows File Explorer locally.
     */
    public function openFolder($id)
    {
        $case = NotarisCase::where('id_kasus', $id)->firstOrFail();

        // Check ownership for freelancer
        if (Auth::user()->role === 'freelancer' && $case->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $isLocal = request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' || env('APP_ENV') === 'local';
        if (!$isLocal) {
            return response()->json(['success' => false, 'message' => 'Membuka folder lokal hanya didukung pada server lokal (localhost). Silakan unduh dokumen langsung dari daftar di bawah.']);
        }

        $archive = Archive::where('id_kasus', $case->id_kasus)->first();
        
        if ($archive && !empty($archive->folder_location)) {
            $localPath = $archive->folder_location;
        } else {
            $localPath = storage_path('app/public/case-files/' . $case->id_kasus);
        }

        // Check if it's a physical storage text (e.g. "Rak Utama, Map Biru A")
        $isPhysical = strpos($localPath, '/') === false && strpos($localPath, '\\') === false;
        if ($isPhysical) {
            return response()->json(['success' => false, 'message' => 'Folder ini adalah folder fisik: ' . $localPath]);
        }

        // Create folder if it doesn't exist
        if (!file_exists($localPath)) {
            try {
                mkdir($localPath, 0777, true);
            } catch (\Exception $e) {
                // fall back to default storage directory
                $localPath = storage_path('app/public/case-files/' . $case->id_kasus);
                if (!file_exists($localPath)) {
                    mkdir($localPath, 0777, true);
                }
            }
        }

        $localPath = str_replace('/', DIRECTORY_SEPARATOR, $localPath);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Safe execution of start explorer
            pclose(popen("start explorer " . escapeshellarg($localPath), "r"));
            return response()->json(['success' => true, 'message' => 'Folder berhasil dibuka di File Explorer.']);
        }

        return response()->json(['success' => false, 'message' => 'Sistem operasi tidak mendukung pembukaan explorer otomatis: ' . $localPath]);
    }
}
