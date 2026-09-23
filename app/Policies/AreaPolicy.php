<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;

class AreaPolicy
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
        return $user->hasAnyRole(['Administrador de Empresa', 'Encargado de Área', 'Consulta / Solo Lectura']);
    }

    public function view(User $user, Area $area): bool
    {
        // Admin o Solo lectura de la empresa, o el encargado de esa área específica
        return $user->empresa_id === $area->sucursal->empresa_id || $user->id === $area->encargado_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador de Empresa');
    }

    public function update(User $user, Area $area): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $area->sucursal->empresa_id;
    }

    public function delete(User $user, Area $area): bool
    {
        return $user->hasRole('Administrador de Empresa') && $user->empresa_id === $area->sucursal->empresa_id;
    }
}
