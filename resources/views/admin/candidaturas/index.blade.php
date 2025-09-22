<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Candidaturas</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-50">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Candidaturas</h1>
      <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:underline">Voltar ao site</a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
      <input
        type="text"
        name="q"
        value="{{ $search }}"
        placeholder="Buscar por nome, e-mail ou cargo"
        class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
      >
      <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Buscar</button>
    </form>

    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-3">Nome</th>
            <th class="p-3">E-mail</th>
            <th class="p-3">Telefone</th>
            <th class="p-3">Cargo</th>
            <th class="p-3">Escolaridade</th>
            <th class="p-3">IP</th>
            <th class="p-3">Enviado em</th>
            <th class="p-3">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($candidaturas as $c)
            <tr class="border-t">
              <td class="p-3">{{ $c->nome }}</td>
              <td class="p-3">{{ $c->email }}</td>
              <td class="p-3">{{ $c->telefone }}</td>
              <td class="p-3">{{ $c->cargo }}</td>
              <td class="p-3">{{ $c->escolaridade }}</td>
              <td class="p-3">{{ $c->ip }}</td>
              <td class="p-3">{{ $c->created_at->format('d/m/Y H:i') }}</td>
              <td class="p-3">
                <a
                  href="{{ route('admin.candidaturas.download', $c) }}"
                  class="text-indigo-600 hover:underline"
                >
                  Baixar currículo
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td class="p-3" colspan="8">Nenhum registro encontrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $candidaturas->links() }}
    </div>
  </div>
</body>
</html>