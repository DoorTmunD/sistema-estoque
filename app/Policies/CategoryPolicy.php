<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    // Permissões organizadas
    protected $manageLevels = ['super-admin', 'adm', 'operador'];
    protected $viewLevels = ['super-admin', 'adm', 'operador', 'common'];

    // Qualquer um pode ver lista
    public function viewAny(User $user): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    // Qualquer um pode ver detalhe
    public function view(User $user, Category $category): bool
    {
        return in_array($user->nivel, $this->viewLevels);
    }

    // Só super-admin, adm, operador podem criar
    public function create(User $user): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    // Só super-admin, adm, operador podem editar
    public function update(User $user, Category $category): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    // Só super-admin, adm, operador podem deletar
    public function delete(User $user, Category $category): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    // Se usar soft delete: só quem gerencia pode restaurar
    public function restore(User $user, Category $category): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }

    // Só quem gerencia pode excluir permanentemente
    public function forceDelete(User $user, Category $category): bool
    {
        return in_array($user->nivel, $this->manageLevels);
    }
}