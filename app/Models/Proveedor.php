<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'tbproveedor';
    protected $primaryKey = 'proveedor_id';
    public $timestamps = false; // si la tabla no tiene created_at/updated_at

    // Si quieres relación inversa con insumos
    public function insumos()
    {
        return $this->belongsToMany(\App\Models\Insumo::class, 'tbproveedor_insumo', 'proveedor_id', 'insumo_id');
    }
}