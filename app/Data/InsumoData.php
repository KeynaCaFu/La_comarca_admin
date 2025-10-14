<?php

namespace App\Data;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsumoData
{
    protected $table = 'tbinsumo';
    protected $pivot = 'tbproveedor_insumo';

    public function all(array $filters = [])
    {
        $q = DB::table($this->table);

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
        $insumo = DB::table($this->table)->where('insumo_id', $id)->first();
        if ($insumo) {
            $proveedores = DB::table('tbproveedor')
                ->join($this->pivot, 'tbproveedor.proveedor_id', '=', $this->pivot.'.proveedor_id')
                ->where($this->pivot.'.insumo_id', $id)
                ->select('tbproveedor.*')
                ->get();
            $insumo->proveedores = $proveedores;
        }
        return $insumo;
    }

    public function create(array $data, array $proveedores = [])
    {
        $id = DB::table($this->table)->insertGetId($data);
        if ($id && count($proveedores) > 0) {
            $rows = [];
            foreach ($proveedores as $p) {
                $rows[] = ['insumo_id' => $id, 'proveedor_id' => $p];
            }
            DB::table($this->pivot)->insert($rows);
        }
        return $id;
    }

    public function update($id, array $data, array $proveedores = null)
    {
        DB::table($this->table)->where('insumo_id', $id)->update($data);

        if (is_array($proveedores)) {
            // sync pivot: eliminar y volver a insertar
            DB::table($this->pivot)->where('insumo_id', $id)->delete();
            if (count($proveedores) > 0) {
                $rows = [];
                foreach ($proveedores as $p) {
                    $rows[] = ['insumo_id' => $id, 'proveedor_id' => $p];
                }
                DB::table($this->pivot)->insert($rows);
            }
        }

        return $this->find($id);
    }

    public function delete($id)
    {
        DB::table($this->pivot)->where('insumo_id', $id)->delete();
        return DB::table($this->table)->where('insumo_id', $id)->delete();
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