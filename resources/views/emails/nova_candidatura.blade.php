@component('mail::message')
# Nova Candidatura

- Nome: {{ $candidatura->nome }}
- E-mail: {{ $candidatura->email }}
- Telefone: {{ $candidatura->telefone }}
- Cargo desejado: {{ $candidatura->cargo }}
- Escolaridade: {{ $candidatura->escolaridade }}
- Observações: {{ $candidatura->observacoes ?: '-' }}
- IP: {{ $candidatura->ip }}
- Data/Hora do envio: {{ $candidatura->created_at->format('d/m/Y H:i') }}

O currículo foi anexado a este e-mail.
@endcomponent