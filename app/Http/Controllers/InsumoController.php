<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\InsumoData;
use App\Data\ProveedorData;


class InsumoController extends Controller
{
    protected $insumoData;
    protected $proveedorData;

    public function __construct(InsumoData $insumoData, ProveedorData $proveedorData)
    {
        $this->insumoData = $insumoData;
        $this->proveedorData = $proveedorData;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['buscar','estado','stock','vencimiento']);
        $insumos = $this->insumoData->all($filters);
        $totales = $this->insumoData->countTotals();
        $proveedores = $this->proveedorData->all();

        return view('insumos.index', compact('insumos','totales','proveedores'));
    }

    public function show($id)
    {
        $insumo = $this->insumoData->find($id);
        if (! $insumo) {
            return redirect()->route('insumos.index')->with('warning', 'Insumo no encontrado.');
        }
        return response()->json($insumo);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|string',
        ]);

        $proveedores = $request->input('proveedores', []);
        $id = $this->insumoData->create($data, $proveedores);

        return redirect()->route('insumos.index')->with('success', 'Insumo creado.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|string',
        ]);

        $proveedores = $request->input('proveedores', null); // null => no modificar pivot
        $this->insumoData->update($id, $data, $proveedores);

        return redirect()->route('insumos.index')->with('success', 'Insumo actualizado.');
    }

    public function destroy($id)
    {
        $this->insumoData->delete($id);
        return redirect()->route('insumos.index')->with('success', 'Insumo eliminado.');
    }
}