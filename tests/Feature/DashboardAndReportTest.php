<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UnidadesMedidaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UnidadesMedidaSeeder::class);
    }

    public function test_user_can_view_dashboard_kpis(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company X', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewHas('totalItems');
        $response->assertViewHas('itemsBajoMinimo');
    }

    public function test_user_can_view_reports(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company X', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $responseInventario = $this->actingAs($user)->get('/reportes/inventario');
        $responseInventario->assertOk();

        $responseMovimientos = $this->actingAs($user)->get('/reportes/movimientos');
        $responseMovimientos->assertOk();
    }

    public function test_user_can_download_excel_and_pdf_reports(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company X', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $responseExcel = $this->actingAs($user)->get('/reportes/inventario/exportar-excel');
        $responseExcel->assertOk();

        $responsePdf = $this->actingAs($user)->get('/reportes/inventario/exportar-pdf');
        $responsePdf->assertOk();
    }
}
