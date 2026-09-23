<?php

namespace App\Policies;

use App\Models\UnidadMedida;
use App\Models\User;

class UnidadMedidaPolicy
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

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function update(User $user, UnidadMedida $unidadMedida): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function delete(User $user, UnidadMedida $unidadMedida): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }
}
