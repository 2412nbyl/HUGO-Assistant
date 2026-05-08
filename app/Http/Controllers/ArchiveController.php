<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\NotarisCase;
use App\Models\Client;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        $archives = Archive::latest()->paginate(10);
        return view('archives.index', compact('archives'));
    }

    public function create()
    {
        $cases = NotarisCase::all();
        $clients = Client::all();
        return view('archives.create', compact('cases', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kasus'        => 'required|string',
            'id_klien'        => 'required|string',
            'client_name'     => 'required|string',
            'folder_location' => 'required|string',
        ]);

        Archive::create($validated);

        return redirect()->route('archives.index')->with('success', 'Arsip berhasil ditambahkan.');
    }

    public function edit(Archive $archive)
    {
        $cases = NotarisCase::all();
        $clients = Client::all();
        return view('archives.edit', compact('archive', 'cases', 'clients'));
    }

    public function update(Request $request, Archive $archive)
    {
        $validated = $request->validate([
            'id_kasus'        => 'required|string',
            'id_klien'        => 'required|string',
            'client_name'     => 'required|string',
            'folder_location' => 'required|string',
        ]);

        $archive->update($validated);

        return redirect()->route('archives.index')->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy(Archive $archive)
    {
        $archive->delete();
        return redirect()->route('archives.index')->with('success', 'Arsip berhasil dihapus.');
    }
}
