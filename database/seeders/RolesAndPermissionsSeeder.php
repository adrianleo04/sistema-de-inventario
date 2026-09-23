<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos
        $permissions = [
            // Empresas
            'empresas.ver',
            'empresas.crear',
            'empresas.editar',
            'empresas.eliminar',
            // Sucursales
            'sucursales.ver',
            'sucursales.crear',
            'sucursales.editar',
            'sucursales.eliminar',
            // Áreas
            'areas.ver',
            'areas.crear',
            'areas.editar',
            'areas.eliminar',
            // Categorías, Unidades y Proveedores
            'catalogos.ver',
            'catalogos.crear',
            'catalogos.editar',
            'catalogos.eliminar',
            // Ítems
            'items.ver',
            'items.crear',
            'items.editar',
            'items.eliminar',
            // Movimientos
            'movimientos.ver',
            'movimientos.registrar',
            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            // Reportes y Dashboard
            'reportes.ver',
            'reportes.exportar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Administrador']);
        $superAdmin->givePermissionTo(Permission::all());

        $adminEmpresa = Role::firstOrCreate(['name' => 'Administrador de Empresa']);
        $adminEmpresa->givePermissionTo([
            'sucursales.ver', 'sucursales.crear', 'sucursales.editar', 'sucursales.eliminar',
            'areas.ver', 'areas.crear', 'areas.editar', 'areas.eliminar',
            'catalogos.ver', 'catalogos.crear', 'catalogos.editar', 'catalogos.eliminar',
            'items.ver', 'items.crear', 'items.editar', 'items.eliminar',
            'movimientos.ver', 'movimientos.registrar',
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'reportes.ver', 'reportes.exportar',
        ]);

        $encargadoArea = Role::firstOrCreate(['name' => 'Encargado de Área']);
        $encargadoArea->givePermissionTo([
            'areas.ver',
            'items.ver',
            'movimientos.ver',
            'movimientos.registrar',
            'reportes.ver',
        ]);

        $soloLectura = Role::firstOrCreate(['name' => 'Consulta / Solo Lectura']);
        $soloLectura->givePermissionTo([
            'items.ver',
            'movimientos.ver',
            'reportes.ver',
        ]);
    }
}
