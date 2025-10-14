<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'tbproveedor';
    protected $primaryKey = 'proveedor_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'direccion',
        'total_compras',
        'estado'
    ];

    // Relación inversa con insumos
    public function insumos()
    {
        return $this->belongsToMany(\App\Models\Insumo::class, 'tbproveedor_insumo', 'proveedor_id', 'insumo_id');
    }
}