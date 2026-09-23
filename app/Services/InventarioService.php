<?php

namespace App\Services;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    /**
     * Registrar una entrada de inventario a un área específica.
     */
    public function registrarEntrada(int $itemId, int $areaDestinoId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw ValidationException::withMessages(['cantidad' => 'La cantidad debe ser mayor a cero.']);
        }

        return DB::transaction(function () use ($itemId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            $inventario = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );

            $inventario->increment('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'area_origen_id' => null,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?? 'Ingreso de nuevo stock',
            ]);
        });
    }

    /**
     * Registrar una salida de inventario de un área específica.
     */
    public function registrarSalida(int $itemId, int $areaOrigenId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw ValidationException::withMessages(['cantidad' => 'La cantidad debe ser mayor a cero.']);
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $cantidad, $usuarioId, $motivo) {
            $inventario = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->first();

            $stockActual = $inventario ? (float) $inventario->cantidad : 0.0;

            if ($stockActual < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente en el área seleccionada. Stock disponible: {$stockActual}, Intentando retirar: {$cantidad}.",
                ]);
            }

            $inventario->decrement('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => null,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?? 'Salida de stock',
            ]);
        });
    }

    /**
     * Trasladar inventario entre dos áreas de la misma empresa.
     * Al completarse, el encargado del área destino pasa a ser automáticamente el responsable del inventario trasladado.
     */
    public function registrarTraslado(int $itemId, int $areaOrigenId, int $areaDestinoId, float $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($areaOrigenId === $areaDestinoId) {
            throw ValidationException::withMessages(['area_destino_id' => 'El área origen y área destino no pueden ser la misma.']);
        }

        if ($cantidad <= 0) {
            throw ValidationException::withMessages(['cantidad' => 'La cantidad a trasladar debe ser mayor a cero.']);
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            $areaOrigen = Area::findOrFail($areaOrigenId);
            $areaDestino = Area::findOrFail($areaDestinoId);

            // Validar que ambas áreas pertenecen a la misma empresa
            if ($areaOrigen->sucursal->empresa_id !== $areaDestino->sucursal->empresa_id) {
                throw ValidationException::withMessages(['area_destino_id' => 'Las áreas de traslado deben pertenecer a la misma empresa.']);
            }

            // Validar stock disponible en área origen
            $inventarioOrigen = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->first();

            $stockOrigen = $inventarioOrigen ? (float) $inventarioOrigen->cantidad : 0.0;

            if ($stockOrigen < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => "No hay stock suficiente en el área origen '{$areaOrigen->nombre}'. Disponible: {$stockOrigen}, solicitado: {$cantidad}.",
                ]);
            }

            // Descontar origen
            $inventarioOrigen->decrement('cantidad', $cantidad);

            // Incrementar destino (re-asignando automáticamente responsabilidad al encargado del área destino)
            $inventarioDestino = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );

            $inventarioDestino->increment('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'traslado',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?? "Traslado de {$areaOrigen->nombre} hacia {$areaDestino->nombre}",
            ]);
        });
    }

    /**
     * Registrar un ajuste manual de inventario (positivo o negativo) con motivo obligatorio.
     */
    public function registrarAjuste(int $itemId, int $areaId, float $cantidadAjuste, int $usuarioId, string $motivo): MovimientoInventario
    {
        if (empty(trim($motivo))) {
            throw ValidationException::withMessages(['motivo' => 'El motivo es obligatorio para registrar un ajuste de inventario.']);
        }

        if ($cantidadAjuste == 0) {
            throw ValidationException::withMessages(['cantidad' => 'La cantidad de ajuste no puede ser cero.']);
        }

        return DB::transaction(function () use ($itemId, $areaId, $cantidadAjuste, $usuarioId, $motivo) {
            $inventario = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaId],
                ['cantidad' => 0]
            );

            $stockActual = (float) $inventario->cantidad;
            $nuevoStock = $stockActual + $cantidadAjuste;

            if ($nuevoStock < 0) {
                throw ValidationException::withMessages([
                    'cantidad' => "El ajuste negativo excede el stock disponible. Stock actual: {$stockActual}, ajuste: {$cantidadAjuste}.",
                ]);
            }

            $inventario->cantidad = $nuevoStock;
            $inventario->save();

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'ajuste',
                'cantidad' => $cantidadAjuste,
                'area_origen_id' => $cantidadAjuste < 0 ? $areaId : null,
                'area_destino_id' => $cantidadAjuste > 0 ? $areaId : null,
                'usuario_id' => $usuarioId,
                'motivo' => "[Ajuste] " . $motivo,
            ]);
        });
    }
}
