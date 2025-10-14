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

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo creado exitosamente',
                'id' => $id
            ]);
        }

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

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo actualizado exitosamente'
            ]);
        }

        return redirect()->route('insumos.index')->with('success', 'Insumo actualizado.');
    }

    public function destroy($id)
    {
        $this->insumoData->delete($id);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo eliminado exitosamente'
            ]);
        }
        
        return redirect()->route('insumos.index')->with('success', 'Insumo eliminado.');
    }

    // Método para cargar el contenido del modal de detalles
    public function showModal($id)
    {
        $insumo = $this->insumoData->find($id);
        if (!$insumo) {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
        return view('insumos.partials.show-modal', compact('insumo'));
    }

    // Método para cargar el contenido del modal de editar
    public function editModal($id)
    {
        $insumo = $this->insumoData->find($id);
        if (!$insumo) {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
        $proveedores = $this->proveedorData->all();
        return view('insumos.partials.edit-modal', compact('insumo', 'proveedores'));
    }
}