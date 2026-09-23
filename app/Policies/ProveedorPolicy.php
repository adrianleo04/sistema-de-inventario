<?php

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\User;

class ProveedorPolicy
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

    public function view(User $user, Proveedor $proveedor): bool
    {
        return $user->empresa_id === $proveedor->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->can('catalogos.crear');
    }

    public function update(User $user, Proveedor $proveedor): bool
    {
        return $user->can('catalogos.editar') && $user->empresa_id === $proveedor->empresa_id;
    }

    public function delete(User $user, Proveedor $proveedor): bool
    {
        return $user->can('catalogos.eliminar') && $user->empresa_id === $proveedor->empresa_id;
    }
}
