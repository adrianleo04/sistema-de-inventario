<?php

namespace App\Policies;

use App\Models\MovimientoInventario;
use App\Models\User;

class MovimientoInventarioPolicy
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
        return $user->can('movimientos.ver') || $user->can('reportes.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('movimientos.registrar');
    }
}
