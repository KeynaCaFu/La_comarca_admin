<?php

namespace App\Data;

use Illuminate\Support\Facades\DB;

class ProveedorData
{
    protected $table = 'tbproveedor';

    public function all()
    {
        return DB::table($this->table)->get();
    }

    public function find($id)
    {
        return DB::table($this->table)->where('proveedor_id', $id)->first();
    }

    public function create(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function update($id, array $data)
    {
        return DB::table($this->table)->where('proveedor_id', $id)->update($data);
    }

    public function delete($id)
    {
        return DB::table($this->table)->where('proveedor_id', $id)->delete();
    }
}