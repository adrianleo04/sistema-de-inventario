<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Administrador')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('catalogos.ver');
    }

    public function view(User $user, Categoria $categoria): bool
    {
        return $user->empresa_id === $categoria->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->can('catalogos.crear');
    }

    public function update(User $user, Categoria $categoria): bool
    {
        return $user->can('catalogos.editar') && $user->empresa_id === $categoria->empresa_id;
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->can('catalogos.eliminar') && $user->empresa_id === $categoria->empresa_id;
    }
}
