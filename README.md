# Criador: Nadson Santos Nascimento 
# Sistema de Cadastro e Envio de Currículos (Laravel)

Aplicação web para receber currículos via formulário, validar dados, armazenar no banco de dados, anexar o arquivo enviado e disparar um e-mail com todas as informações. Inclui área administrativa protegida para listar candidaturas (com busca e paginação) e baixar o currículo. Suporta envio de e-mail assíncrono (fila/queue).

Projeto desenvolvido para UGTSIC - SESAP/RN (2025).

## Tecnologias
- PHP 8.2+
- Laravel 11
- Laravel Breeze (Blade) – autenticação básica e setup do Tailwind
- Tailwind CSS + Vite (build do front-end)
- SQLite (ambiente de desenvolvimento)
- Mailtrap (sugerido para testes de e-mail)
- Laravel Queue (driver database) — opcional para envio assíncrono

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+ e npm

## Instalação e Execução (passo a passo)
1. Clonar o repositório e entrar na pasta do projeto
   ```bash
   git clone <URL_DO_SEU_REPO>
   cd <PASTA_PROJETO>
   ```

2. Instalar dependências do PHP e criar o arquivo .env
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Configurar o banco de dados (SQLite)
   - Criar o arquivo do banco:
     ```bash
     mkdir -p database
     # Windows PowerShell (se precisar):
     # New-Item -ItemType Directory .\\database -Force
     # New-Item -ItemType File .\\database\\database.sqlite -Force
     touch database/database.sqlite
     ```
   - No arquivo `.env`, configure:
     ```env
     DB_CONNECTION=sqlite
     DB_DATABASE=database/database.sqlite
     ```
   - Rodar as migrações:
     ```bash
     php artisan migrate
     ```

4. Instalar dependências do front-end e iniciar o Vite
   ```bash
   npm install
   npm run dev
   ```

5. Subir o servidor do Laravel
   ```bash
   php artisan serve
   ```
   - Acesse: http://127.0.0.1:8000
   - Formulário: http://127.0.0.1:8000/candidaturas/create

## Configuração de E-mail (ex.: Mailtrap)
Para desenvolvimento, recomendamos Mailtrap (Email Testing). Copie as credenciais SMTP da sua inbox no Mailtrap e ajuste no `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<SEU_USERNAME_MAILTRAP>
MAIL_PASSWORD=<SEU_PASSWORD_MAILTRAP>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@curriculos.test"
MAIL_FROM_NAME="Portal de Currículos"

# Destinatário do RH (quem recebe o e-mail com anexo):
HR_MAIL_TO="seu.email@exemplo.com"
```
Depois de alterar o .env, limpe o cache de configuração:
```bash
php artisan config:clear
```
Observação: se a rede bloquear a porta 2525, teste `MAIL_PORT=587` com `MAIL_ENCRYPTION=tls`.

## Funcionalidades
- Formulário com os campos: Nome, E-mail, Telefone, Cargo desejado, Escolaridade (select), Observações (opcional) e Arquivo do currículo.
- Validações no back-end:
  - Todos obrigatórios, exceto Observações.
  - Arquivo: somente `.pdf`, `.doc` ou `.docx`.
  - Tamanho máximo do arquivo: 1MB.
- Armazenamento:
  - Currículos salvos em `storage/app/curriculos` (disco local do Laravel).
  - Banco registra `ip` do remetente e `created_at` (data/hora do envio).
- E-mail:
  - Enviado para `HR_MAIL_TO` (ou `MAIL_FROM_ADDRESS` se `HR_MAIL_TO` não estiver definido), com o arquivo do currículo anexado e os dados da candidatura no corpo da mensagem.
- Área administrativa (bônus):
  - Listagem de candidaturas com busca (por nome, e-mail, cargo) e paginação.
  - Download do currículo diretamente do storage (sem necessidade de `storage:link`).

## Rotas principais
- `GET /` → redireciona para o formulário
- `GET /candidaturas/create` → exibe o formulário
- `POST /candidaturas` → processa o envio (valida, salva, envia e-mail)
- `GET /admin/candidaturas` (auth) → lista candidaturas (busca/paginação)
- `GET /admin/candidaturas/{candidatura}/download` (auth) → baixa o currículo

Listar todas as rotas:
```bash
php artisan route:list
```

## Área Administrativa (bônus 1)
- Protegida por autenticação (Breeze). Acesse `/register` para criar um usuário e `/login` para entrar.
- Acesse `/admin/candidaturas` para ver a lista.
- Caixa de busca filtra por nome, e-mail ou cargo (parâmetro `q`).
- Paginação padrão de 10 itens por página.
- Link “Baixar currículo” usa `Storage::download`, então não é necessário executar `php artisan storage:link` para essa funcionalidade.

## Envio de E-mail Assíncrono (bônus 2)
Permite que o e-mail seja enfileirado e enviado em background, evitando travar o submit do formulário.

1) Habilitar driver de fila `database` no `.env`:
```env
QUEUE_CONNECTION=database
```
Depois:
```bash
php artisan config:clear
```

2) Criar tabelas da fila e migrar:
```bash
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
```

3) Deixar o Mailable processar em fila
Na classe `App\Mail\NovaCandidaturaMail`, implemente `ShouldQueue`:
```php
use Illuminate\Contracts\Queue\ShouldQueue;

class NovaCandidaturaMail extends Mailable implements ShouldQueue
{
    // ...
}
```

4) Enfileirar o envio no Controller
No `store()` troque `send()` por `queue()`:
```php
Mail::to($recipient)->queue(new NovaCandidaturaMail($candidatura));
```

5) Rodar o worker da fila (novo terminal):
```bash
php artisan queue:work
```

Dicas:
- Para ver falhas: tabela `failed_jobs`. Reprocessar: `php artisan queue:retry all`.
- Se preferir não enviar e-mail real em dev, use `MAIL_MAILER=log` (grava no log em `storage/logs/laravel.log`).

## Testes manuais rápidos
1. Enviar arquivo > 1MB → deve falhar (validação).
2. Enviar `.txt` → deve falhar (tipo inválido).
3. Deixar campo obrigatório vazio → deve falhar.
4. Com tudo correto → deve exibir "Candidatura enviada com sucesso!", criar registro na tabela `candidaturas`, salvar arquivo em `storage/app/curriculos` e enviar (ou enfileirar) o e-mail.
5. Admin: acessar `/admin/candidaturas`, testar busca (`?q=termo`), paginação (`?page=2`) e baixar currículo.

## Onde ficam os arquivos enviados?
- Diretório: `storage/app/curriculos`.
- Nome salvo em `curriculo_path` e nome original em `curriculo_original` (tabela `candidaturas`).

## Solução de Problemas
- `could not find driver (sqlite)`
  - Habilitar no `php.ini` (verifique com `php --ini`):
    ```ini
    extension=pdo_sqlite
    extension=sqlite3
    ```
  - Abra um novo terminal e confirme com `php -m` se `pdo_sqlite` e `sqlite3` aparecem.

- Caminho do `DB_DATABASE` no Windows (dotenv)
  - Use relativo `DB_DATABASE=database/database.sqlite` ou absoluto com `/` (ex.: `C:/.../database.sqlite`). Evite aspas duplas + barras invertidas.

- SMTP `535 5.7.0 Invalid credentials`
  - Verifique `MAIL_*` no `.env`, copie as credenciais corretas do provedor (ex.: Mailtrap) e rode `php artisan config:clear`.

- Jobs na fila não processam
  - Confirme `QUEUE_CONNECTION=database` (via `php artisan tinker` → `config('queue.default')`).
  - Verifique se o worker está rodando (`php artisan queue:work`).
  - Veja `failed_jobs` e reprocese com `php artisan queue:retry all`.

- Upload falha antes da validação (tamanho)
  - Ajuste no `php.ini`: `upload_max_filesize` e `post_max_size` ≥ 2M. A validação do Laravel continuará limitando a 1MB.

## Como mudar para MySQL (opcional)
No `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
Depois:
```bash
php artisan migrate
```

## Bônus implementados
- [x] Área administrativa: listagem com busca e paginação; download do currículo.
- [x] Envio de e-mail assíncrono via fila (`QUEUE_CONNECTION=database`).

## Possíveis melhorias futuras
- Testes automatizados (feature tests com `Mail::fake` e `Storage::fake`).
- CI com GitHub Actions (rodar testes a cada push/PR).
- Docker para padronizar o ambiente.

## Envio do Projeto
- Publique o código em um repositório (GitHub, GitLab ou outro) e envie o link para: `ugtsicdev@gmail.com`.

## Glossário
- ORM (Eloquent): mapeia tabela do banco em classes PHP.
- Migration: script versionado para criar/alterar tabelas.
- Blade: engine de templates do Laravel (views `.blade.php`).
- Vite: ferramenta de build para CSS/JS (dev server com HMR).
- SMTP: protocolo para envio de e-mails.
- Queue/Worker: fila de tarefas e processo que executa as tarefas em background.
