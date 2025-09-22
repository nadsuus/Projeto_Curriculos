<?php

namespace App\Http\Controllers;

use App\Mail\NovaCandidaturaMail;
use App\Models\Candidatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CandidaturaController extends Controller
{
    public function create()
    {
        $escolaridades = ['Ensino Médio','Graduação','Pós-graduação','Mestrado','Doutorado'];
        return view('candidaturas.create', compact('escolaridades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'         => ['required','string','max:255'],
            'email'        => ['required','email'],
            'telefone'     => ['required','string','max:50'],
            'cargo'        => ['required','string','max:255'],
            'escolaridade' => ['required','in:Ensino Médio,Graduação,Pós-graduação,Mestrado,Doutorado'],
            'observacoes'  => ['nullable','string'],
            'curriculo'    => ['required','file','mimes:pdf,doc,docx','max:1024'], // 1024 KB = 1MB
        ]);

        $file = $request->file('curriculo');
        $storedPath = $file->store('curriculos'); // storage/app/curriculos
        $original = $file->getClientOriginalName();

        $candidatura = Candidatura::create([
            'nome'               => $validated['nome'],
            'email'              => $validated['email'],
            'telefone'           => $validated['telefone'],
            'cargo'              => $validated['cargo'],
            'escolaridade'       => $validated['escolaridade'],
            'observacoes'        => $validated['observacoes'] ?? null,
            'curriculo_path'     => $storedPath,
            'curriculo_original' => $original,
            'ip'                 => $request->ip(),
        ]);

        // Enviar e-mail
        $recipient = env('HR_MAIL_TO', env('MAIL_FROM_ADDRESS'));
        if ($recipient) {
            Mail::to($recipient)->queue(new NovaCandidaturaMail($candidatura));
        }

        return redirect()
            ->route('candidaturas.create')
            ->with('status', 'Candidatura enviada com sucesso!');
    }
}