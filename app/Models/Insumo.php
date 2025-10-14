<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'tbinsumo';
    protected $primaryKey = 'insumo_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'stock_actual',
        'stock_minimo',
        'fecha_vencimiento',
        'unidad_medida',
        'cantidad',
        'precio',
        'estado',
        'descripcion'
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'tbproveedor_insumo', 'insumo_id', 'proveedor_id');
    }
}