# Instruções de Atualização

1. Extraia os arquivos na raiz do seu projeto.
2. Rode `composer dump-autoload` para registrar novos controllers e models.
3. Adicione estas linhas em `routes/web.php` (substituindo o arquivo existente):
   ```php
   // imports para UserController e InventoryController
   use App\Http\Controllers\UserController;
   use App\Http\Controllers\InventoryController;

   // rotas de usuários (perfil super-admin)
   Route::middleware('can:manage-users')->group(function () {
       Route::resource('users', UserController::class)->except(['show']);
   });

   // rotas de estoque
   Route::middleware('can:manage-products')->group(function () {
       Route::resource('inventory', InventoryController::class);
   });
   ```
4. Atualize o componente de navegação em `resources/views/livewire/welcome/navigation.blade.php`:
   - Após o link Dashboard, adicione:
     ```blade
     @can('manage-users')
         <a href="{{ route('users.index') }}">Usuários</a>
     @endcan
     @can('manage-products')
         <a href="{{ route('inventory.index') }}">Estoque</a>
     @endcan
     ```
5. Rode a migration:
   ```bash
   php artisan migrate
   ```
6. Teste as rotas `/users` e `/inventory`.
