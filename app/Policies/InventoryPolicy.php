<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inventory;

class InventoryPolicy
{
    protected $manageLevels = ['super-admin', 'adm', 'operador'];
    protected $viewLevels   = ['super-admin', 'adm', 'operador', 'common'];

    public function viewAny(User $user): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function create(User $user): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function update(User $user, Inventory $inventory): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function delete(User $user, Inventory $inventory): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function restore(User $user, Inventory $inventory): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function forceDelete(User $user, Inventory $inventory): bool
    {
        return $user->nivel === 'super-admin';
    }
}