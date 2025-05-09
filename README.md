# Fase 1 – Sistema de Controle de Estoque (Laravel 11)

Este pacote contém o bootstrap inicial com:

* Laravel 11 configurado (você deve rodar `composer install`)
* Breeze + Livewire + Tailwind (instalação em `install.sh`)
* Layout high‑tech (login, registro) com dark/light
* Modelos, migrations e seeders de Roles & Usuário super-admin
* Middleware de RBAC (`app/Http/Middleware/Role.php`)
* Máscaras de R$‑BRL, CPF e CNPJ (Cleave.js) integradas aos componentes Livewire
* Pipeline GitHub Actions (`.github/workflows/ci.yml`)
* Arquivo `render.yaml` para deploy automático na Render

## Instalação local rápida

```bash
git clone https://github.com/DoorTmunD/sistema-estoque.git
cd sistema-estoque
bash install.sh         # instala dependências PHP/Composer + Frontend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev             # ou `npm run build`
php artisan serve
```

Usuário inicial:
* Email: **admin@example.com**
* Senha: **password**

Troque a senha logo após o primeiro login.

## Deploy no Render

1. Crie um banco PostgreSQL gratuito e copie as variáveis.
2. Crie um Web Service e aponte para este repositório.
3. Build Command:
   ```bash
   composer install --no-dev && php artisan key:generate && php artisan migrate --force && npm ci && npm run build
   ```
4. Start Command:
   ```bash
   php artisan serve --host 0.0.0.0 --port $PORT
   ```
5. Configure as variáveis `.env` (*APP_ENV, APP_KEY, DB_*, APP_URL*).

Pronto!