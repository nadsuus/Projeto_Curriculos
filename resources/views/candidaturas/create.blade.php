<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Enviar Currículo</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-50">
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6">Enviar Currículo</h1>

    @if (session('status'))
      <div class="mb-4 rounded border border-green-300 bg-green-50 text-green-800 p-3">
        {{ session('status') }}
      </div>
    @endif

    <form action="{{ route('candidaturas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium">Nome</label>
        <input type="text" name="nome" value="{{ old('nome') }}" required maxlength="255"
               class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nome') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Telefone</label>
        <input type="tel" name="telefone" value="{{ old('telefone') }}" required maxlength="50"
               class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('telefone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Cargo desejado</label>
        <input type="text" name="cargo" value="{{ old('cargo') }}" required maxlength="255"
               class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('cargo') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Escolaridade</label>
        <select name="escolaridade" required
                class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="">Selecione</option>
          @foreach (['Ensino Médio','Graduação','Pós-graduação','Mestrado','Doutorado'] as $opt)
            <option value="{{ $opt }}" @selected(old('escolaridade')===$opt)>{{ $opt }}</option>
          @endforeach
        </select>
        @error('escolaridade') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Observações (opcional)</label>
        <textarea name="observacoes" rows="3"
                  class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacoes') }}</textarea>
        @error('observacoes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Currículo (.pdf, .doc, .docx, máx. 1MB)</label>
        <input type="file" name="curriculo" required accept=".pdf,.doc,.docx"
               class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('curriculo') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="pt-2">
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
          Enviar
        </button>
      </div>
    </form>
  </div>
</body>
</html>