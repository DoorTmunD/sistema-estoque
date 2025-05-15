# Sistema de Controle de Estoque

Este repositório contém um **sistema de controle de estoque** interno desenvolvido em Laravel + Livewire.

## Índice
- [Visão Geral](#visão-geral)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [Deploy no Render.com](#deploy-no-rendercom)
- [Fluxo de Rotas](#fluxo-de-rotas)
- [Contribuição](#contribuição)

## Visão Geral
Sistema de controle de produtos de uso corporativo (foco em informática), com autenticação por níveis:
- **super-admin**: gerencia usuários, produtos, categorias e fornecedores;
- **adm**: gerencia produtos, categorias e fornecedores;
- **common**: apenas visualização.

## Pré-requisitos
- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM
- Docker (opcional)
- PostgreSQL (local ou remoto)

## Instalação
```bash
# Clonar o repositório
git clone https://github.com/DoorTmunD/sistema-estoque.git
cd sistema-estoque

# Backend
composer install

# Frontend
npm install && npm run build
```

### Rodando com Docker
```bash
docker-compose up -d --build
```

## Configuração
Copie o `.env.example` para `.env` e ajuste:
```
APP_URL=
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
Gere chave da aplicação:
```bash
php artisan key:generate
```

## Uso
```bash
# Migrar banco e seed (somente se necessário)
php artisan migrate --seed

# Servidor local
php artisan serve
npm run dev
```

## Deploy no Render.com
1. Crie um serviço Web no Render, apontando para a branch `main`.
2. Adicione variável `DATABASE_URL` com a URL do PostgreSQL gerado automaticamente.
3. Ajuste o comando de build:
   - **Build Command**: `composer install && npm ci && npm run build`
   - **Start Command**: `php artisan migrate --force && php artisan serve --port $PORT`

## Fluxo de Rotas
- `/` → se não autenticado redireciona para `/login`, senão para `/dashboard`.
- Grupos protegidos por middleware `auth` e Gates (`manage-products`, `manage-users`).

## Contribuição
1. Faça um fork
2. Crie uma branch: `feature/nome-da-feature`
3. Commit suas alterações
4. Abra um Pull Request

```bash
# Rodar linting e testes antes de commitar
composer lint
phpunit
``` 
