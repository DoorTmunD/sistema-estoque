<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use App\Models\InventoryMovement;

class SearchGlobal extends Component
{
    public string $query = '';
    public array $results = [];

    public function updatedQuery(): void
    {
        if (!(config('estocore.global_search_enabled') ?? true)) {
            $this->results = [];
            return;
        }

        $q = trim($this->query);
        $this->results = [];
        if (mb_strlen($q) < 2) return;

        $user = Auth::user();
        if (!$user) return;

        // helper para fallback sem Scout
        $scout = function ($model, $columns = ['name']) use ($q) {
            try {
                if (method_exists($model, 'search')) {
                    return $model::search($q)->take(5)->get();
                }
            } catch (\Throwable $e) {
                // ignora e cai no like
            }
            return $model::query()->where(function ($qq) use ($columns, $q) {
                foreach ($columns as $col) {
                    $qq->orWhere($col, 'like', "%{$q}%");
                }
            })->take(5)->get();
        };

        if ($user->can('viewAny', Product::class)) {
            $this->results['Produtos'] = $scout(Product::class, ['name','description']);
        }

        if ($user->can('viewAny', Category::class)) {
            $this->results['Categorias'] = $scout(Category::class, ['name']);
        }

        if ($user->can('viewAny', Supplier::class)) {
            $this->results['Fornecedores'] = $scout(Supplier::class, ['name']);
        }

        // Users: seu resource não tem 'show', então vamos para index com ?search=
        if ($user->can('viewAny', User::class)) {
            $this->results['Usuários'] = $scout(User::class, ['name','email']);
        }

        // Movimentações: envia para a tela de histórico/tabela
        if ($user->can('viewAny', InventoryMovement::class)) {
            $this->results['Movimentações'] = $scout(InventoryMovement::class, ['notes'])
                ->each(function ($m) {
                    $m->label = $m->type_label . ' · ' . optional($m->product)->name;
                });
        }
    }

    public function goTo(string $type, int $id)
    {
        switch ($type) {
            case 'Produtos':
                // existe products.show — se preferir, troque por edit
                return redirect()->route('products.show', $id);

            case 'Categorias':
                return redirect()->route('categories.show', $id);

            case 'Fornecedores':
                return redirect()->route('suppliers.show', $id);

            case 'Usuários':
                // não há users.show nas rotas — leva ao index com busca por ID
                return redirect()->route('users.index', ['search' => $id]);

            case 'Movimentações':
                // não há movements.show — leva ao index filtrando por produto quando possível
                return redirect()->route('movements.index', ['product_id' => $id]);

            default:
                return null;
        }
    }

    public function render()
    {
        return view('livewire.search-global');
    }
}
