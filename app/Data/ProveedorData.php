<?php

namespace App\Data;

use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;

class ProveedorData
{
    protected $table = 'tbproveedor';

    public function all()
    {
        return Proveedor::with('insumos')->get();
    }

    public function find($id)
    {
        return Proveedor::with('insumos')->find($id);
    }

    public function create(array $data, array $insumos = [])
    {
        $proveedor = Proveedor::create($data);
        
        // Asociar insumos si se proporcionaron
        if (count($insumos) > 0) {
            $proveedor->insumos()->attach($insumos);
        }
        
        return $proveedor->proveedor_id;
    }

    public function update($id, array $data, array $insumos = null)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($data);
        
        // Sincronizar insumos si se proporcionaron
        if (is_array($insumos)) {
            $proveedor->insumos()->sync($insumos);
        }
        
        return true;
    }

    public function delete($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->insumos()->detach();
        return $proveedor->delete();
    }
}