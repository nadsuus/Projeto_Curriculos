<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidaturaAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $query = Candidatura::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%");
            });
        }

        $candidaturas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.candidaturas.index', compact('candidaturas', 'search'));
    }

    public function download(Candidatura $candidatura)
    {
        $path = $candidatura->curriculo_path;

        if (!Storage::exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $filename = $candidatura->curriculo_original ?: basename($path);

        // Baixa direto do storage local (não precisa storage:link)
        return Storage::download($path, $filename);
    }
}