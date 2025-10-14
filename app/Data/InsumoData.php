<?php

namespace App\Data;

use App\Models\Insumo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsumoData
{
    protected $table = 'tbinsumo';
    protected $pivot = 'tbproveedor_insumo';

    public function all(array $filters = [])
    {
        $q = Insumo::with('proveedores');

        if (!empty($filters['buscar'])) {
            $term = '%'.$filters['buscar'].'%';
            $q->where(function($query) use ($term) {
                $query->where('nombre', 'like', $term)
                      ->orWhere('descripcion', 'like', $term);
            });
        }

        if (!empty($filters['estado'])) {
            $q->where('estado', $filters['estado']);
        }

        if (!empty($filters['stock'])) {
            if ($filters['stock'] === 'bajo') {
                $q->whereColumn('stock_actual', '<=', 'stock_minimo');
            }
        }

        if (!empty($filters['vencimiento'])) {
            $now = Carbon::now()->toDateString();
            if ($filters['vencimiento'] === 'por_vencer') {
                $por = Carbon::now()->addDays(30)->toDateString();
                $q->whereBetween('fecha_vencimiento', [$now, $por]);
            } elseif ($filters['vencimiento'] === 'vencidos') {
                $q->where('fecha_vencimiento', '<', $now);
            } elseif ($filters['vencimiento'] === 'buenos') {
                $q->where(function($sub) use ($now) {
                    $sub->whereNull('fecha_vencimiento')
                        ->orWhere('fecha_vencimiento', '>=', $now);
                });
            }
        }

        return $q->get();
    }

    public function find($id)
    {
        return Insumo::with('proveedores')->find($id);
    }

    public function create(array $data, array $proveedores = [])
    {
        $insumo = Insumo::create($data);
        if (count($proveedores) > 0) {
            $insumo->proveedores()->attach($proveedores);
        }
        return $insumo->insumo_id;
    }

    public function update($id, array $data, array $proveedores = null)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->update($data);

        if (is_array($proveedores)) {
            $insumo->proveedores()->sync($proveedores);
        }

        return $insumo->fresh('proveedores');
    }

    public function delete($id)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->proveedores()->detach();
        return $insumo->delete();
    }

    public function countTotals()
    {
        $totals = [];
        $totals['todos'] = DB::table($this->table)->count();
        $totals['disponibles'] = DB::table($this->table)->where('estado', 'Disponible')->count();
        $totals['agotados'] = DB::table($this->table)->where('estado', 'Agotado')->count();
        $totals['vencidos'] = DB::table($this->table)->where('estado', 'Vencido')->count();
        $totals['stock_bajo'] = DB::table($this->table)->whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        $now = Carbon::now()->toDateString();
        $por = Carbon::now()->addDays(30)->toDateString();
        $totals['por_vencer'] = DB::table($this->table)
            ->whereBetween('fecha_vencimiento', [$now, $por])
            ->count();

        return $totals;
    }
}