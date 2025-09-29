<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryMovement;

class InventoryMovementPolicy
{
    protected $manageLevels = ['super-admin', 'adm', 'operador'];
    protected $viewLevels   = ['super-admin', 'adm', 'operador', 'common'];

    public function viewAny(User $user): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function view(User $user, InventoryMovement $movement): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    public function create(User $user): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function update(User $user, InventoryMovement $movement): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function delete(User $user, InventoryMovement $movement): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function restore(User $user, InventoryMovement $movement): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    public function forceDelete(User $user, InventoryMovement $movement): bool
    {
        return $user->nivel === 'super-admin';
    }
}