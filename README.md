Sistema de Controle de Estoque

Sistema web moderno para gestão interna de estoque, voltado para uso corporativo e com foco em personalização, segurança, UX e extensibilidade.

🚀 Tecnologias Utilizadas

Laravel 10+ (PHP 8.2)

Livewire (UX reativa)

Tailwind CSS + Flowbite (UI/UX premium, dark mode, animações)

ApexCharts (dashboards e gráficos)

Spatie Activitylog (auditoria de ações)

PostgreSQL (default)

Alpine.js (animações, interação front)

Lottie (motion graphics e feedback visual)

Docker (deploy opcional)

🏗️ Funcionalidades Principais

Gestão de Produtos: cadastro, edição, exclusão, busca e visualização.

Gestão de Categorias e Fornecedores

Movimentação de Estoque: entrada/saída, histórico detalhado, exportação CSV.

Dashboard Moderno: cards animados, gráficos interativos, dark/light mode.

Usuários e Perfis: permissões diferenciadas (admin, operador, visualizador).

Logs de Auditoria: monitoramento de todas ações críticas (via Spatie Activitylog).

Feedback Visual: toasts animados, empty state com Lottie, loaders premium.

Responsivo e Mobile Ready

Autenticação segura

Quick Actions e atalhos de teclado

📝 Features Futuras

Veja também featuresFuturas.md para roadmap/backlog detalhado.

Upload de imagens/anexos em produtos, movimentações e fornecedores

Busca global (modal, Ctrl+K)

Permissões granulares por role/usuário

Relatórios exportáveis (PDF/CSV) e dashboard personalizável

Notificações inteligentes e timeline visual de movimentações

Integração API, BIP/QR code para movimentações, módulo de requisições de compra

⚙️ Instalação

Clone o projeto:

git clone <repo-url>
cd sistema-estoque

Instale dependências PHP/JS:

composer install
npm install && npm run build

Configure .env e banco de dados:

Copie .env.example para .env

Defina DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

Gere key e rode migrations/seeders:

php artisan key:generate
php artisan migrate --seed

(Opcional) Rode via Docker:

docker-compose up --build

Suba servidor local:

php artisan serve

Acesse via navegador: http://localhost:8000

🔐 Usuários e Permissões

O sistema possui autenticação via e-mail/senha.

Perfis: super-admin, admin, operador, visualizador.

Apenas admins podem gerenciar usuários, categorias e fornecedores.

Controle detalhado de permissões em breve via spatie/laravel-permission.

Usuário padrão para login inicial:

E-mail: admin@empresa.com

Senha: senha-segura

📊 Dashboard

Cards: estoque total, produtos abaixo do ideal, categorias, fornecedores.

Gráficos: movimentação (barras), estoque por categoria (pizza), top 5 produtos (horizontal).

Motion: efeitos neon, animações em tempo real, feedback por lottie/toast.

📦 Estrutura de Pastas Importante

resources/views/products - CRUD de produtos

resources/views/categories - CRUD de categorias

resources/views/suppliers - CRUD de fornecedores

resources/views/users - Gestão de usuários

resources/views/dashboard-livewire.blade.php - Dashboard principal

resources/views/livewire - Components Livewire

app/Http/Controllers - Controllers (MVC)

app/Models - Models principais

database/migrations, database/seeders - Estrutura e dados iniciais

🌍 Deploy

Deploy em nuvem recomendado:

Render, AWS, Digital Ocean, Vercel (via Docker)

Docker ready (docker-compose.yml)

Exporte variáveis de ambiente para produção

🤝 Contribuição

Fork o projeto, crie sua branch e envie um PR!

Sempre documente as novas features no README.md e no featuresFuturas.md.

Use PSR-12, siga boas práticas Laravel

Faça testes antes de enviar PRs

👨‍💻 Contato e Suporte

Dúvidas, bugs ou sugestões? Abra uma issue no GitHub ou entre em contato com o mantenedor do projeto.

📚 Documentação complementar

Veja também:

UPDATE_INSTRUCTIONS.md (passo a passo para atualização do sistema)

featuresFuturas.md (backlog/contexto avançado para ChatGPT)