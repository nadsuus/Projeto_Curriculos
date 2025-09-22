<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Candidatura;
use App\Mail\NovaCandidaturaMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidaturaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function submeter_candidatura_valida_salva_registro_arquivo_e_enfileira_email()
    {
        Mail::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $response = $this->post(route('candidaturas.store'), [
            'nome' => 'Maria Teste',
            'email' => 'maria@example.com',
            'telefone' => '11999999999',
            'cargo' => 'Desenvolvedora',
            'escolaridade' => 'Graduação',
            'observacoes' => 'Observação de teste',
            'curriculo' => $file,
        ]);

        $response->assertRedirect(route('candidaturas.create'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('candidaturas', [
            'email' => 'maria@example.com',
            'cargo' => 'Desenvolvedora',
        ]);

        $c = Candidatura::first();
        $this->assertNotNull($c);
        Storage::disk('local')->assertExists($c->curriculo_path);

        Mail::assertQueued(NovaCandidaturaMail::class, function ($mailable) use ($c) {
            return $mailable->candidatura->id === $c->id;
        });
    }

    #[Test]
    public function valida_arquivo_muito_grande_e_tipo_invalido()
    {
        // Arquivo > 1MB deve falhar
        $big = UploadedFile::fake()->create('cv.pdf', 2048, 'application/pdf');
        $resp1 = $this->post(route('candidaturas.store'), [
            'nome' => 'João',
            'email' => 'joao@example.com',
            'telefone' => '11988887777',
            'cargo' => 'Analista',
            'escolaridade' => 'Graduação',
            'curriculo' => $big,
        ]);
        $resp1->assertSessionHasErrors('curriculo');

        // Extensão inválida deve falhar
        $txt = UploadedFile::fake()->create('cv.txt', 10, 'text/plain');
        $resp2 = $this->post(route('candidaturas.store'), [
            'nome' => 'João',
            'email' => 'joao@example.com',
            'telefone' => '11988887777',
            'cargo' => 'Analista',
            'escolaridade' => 'Graduação',
            'curriculo' => $txt,
        ]);
        $resp2->assertSessionHasErrors('curriculo');
    }
}