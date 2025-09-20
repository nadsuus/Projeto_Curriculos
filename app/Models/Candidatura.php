<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome','email','telefone','cargo','escolaridade',
        'observacoes','curriculo_path','curriculo_original','ip'
    ];
}