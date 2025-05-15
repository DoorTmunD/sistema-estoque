# Fluxos de Funcionalidade

## Autenticação
- **Login**: Livewire Volt Form em `/login`.
- **Logout**: Ação Livewire `Actions\Logout` chamada no header.
- **Registro**: Apenas super-admin registra novos usuários.

## Autorização
- **Gates** (definidos em `AuthServiceProvider`):
  - `manage-users`: perfil `super-admin`;
  - `manage-products`: perfis `super-admin` e `adm`;

## CRUD de Produtos
1. Usuário acessa `/products`.
2. `ProductController@index` retorna `products.index`.
3. Formulários de criação/edição usam Livewire para validação em tempo real.
4. Salvamento via `store`/`update` do controller.

## Dashboard
- Apresenta contadores e gráfico de vendas:
  - Controller: `DashboardController@index`;
  - Componente Livewire: `Dashboard`;
  - Biblioteca: ConsoleTVs/Charts + Chart.js.
