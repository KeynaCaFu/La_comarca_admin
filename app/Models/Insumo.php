<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'tbinsumo';
    protected $primaryKey = 'insumo_id';
    public $timestamps = false;

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'tbproveedor_insumo', 'insumo_id', 'proveedor_id');
    }
}