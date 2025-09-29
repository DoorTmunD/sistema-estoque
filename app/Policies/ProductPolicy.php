<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    protected $manageLevels = ['super-admin', 'adm', 'operador'];
    protected $viewLevels   = ['super-admin', 'adm', 'operador', 'common'];

    public function viewAny(User $user): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function view(User $user, Product $product): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function create(User $user): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function update(User $user, Product $product): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function delete(User $user, Product $product): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function restore(User $user, Product $product): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->nivel === 'super-admin';
    }
}