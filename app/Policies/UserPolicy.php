<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    // Só super-admin pode ver todos
    public function viewAny(User $user)
    {
        return $user->nivel === 'super-admin' || $user->nivel === 'adm';
    }

    // Pode visualizar próprio perfil, ou se for admin/super-admin
    public function view(User $user, User $target)
    {
        return $user->id === $target->id || $user->nivel === 'super-admin' || $user->nivel === 'adm';
    }

    // Só super-admin pode criar admin/super-admin
    public function create(User $user)
    {
        return $user->nivel === 'super-admin' || $user->nivel === 'adm';
    }

    // Pode editar se for admin e o alvo não for admin/super-admin, ou se for super-admin
    public function update(User $user, User $target)
    {
        if ($user->nivel === 'super-admin') return true;
        if ($user->nivel === 'adm' && $target->nivel === 'common') return true;
        // admin não pode editar outro admin nem super-admin
        return $user->id === $target->id;
    }

    // Regra de OURO: só pode deletar se o alvo for inferior
    public function delete(User $user, User $target)
    {
        if ($user->nivel === 'super-admin') {
            // super-admin pode deletar qualquer um, inclusive outro super-admin se quiser
            return $user->id !== $target->id; // pode se quiser permitir deletar a si mesmo, deixe só return true;
        }
        if ($user->nivel === 'adm') {
            // admin só pode deletar common
            return $target->nivel === 'common';
        }
        // comum nunca pode deletar nada
        return false;
    }
}