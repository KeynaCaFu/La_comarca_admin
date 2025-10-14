<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ProveedorData;
use App\Data\InsumoData; // si necesitas listar insumos en formularios

class ProveedorController extends Controller
{
    protected $proveedorData;
    protected $insumoData;

    public function __construct(ProveedorData $proveedorData, InsumoData $insumoData)
    {
        $this->proveedorData = $proveedorData;
        $this->insumoData = $insumoData;
    }

    public function index()
    {
        $proveedores = $this->proveedorData->all();
        $insumos = $this->insumoData->all();
        return view('proveedores.index', compact('proveedores', 'insumos'));
    }

    public function show($id)
    {
        $proveedor = $this->proveedorData->find($id);
        if (! $proveedor) {
            return redirect()->route('proveedores.index')->with('warning', 'Proveedor no encontrado.');
        }
        return response()->json($proveedor);
    }

    public function create()
    {
        $insumos = $this->insumoData->all(); // Obtener TODOS los insumos
        return view('proveedores.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'correo' => 'nullable|email|max:255',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'nullable|string',
        ]);

        // Obtener los insumos seleccionados
        $insumos = $request->input('insumos', []);
        
        $id = $this->proveedorData->create($data, $insumos);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado exitosamente',
                'id' => $id
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'correo' => 'nullable|email|max:255',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'nullable|string',
        ]);

        // Obtener los insumos seleccionados (null = no modificar relaciones)
        $insumos = $request->input('insumos', null);
        
        $this->proveedorData->update($id, $data, $insumos);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado exitosamente'
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy($id)
    {
        $this->proveedorData->delete($id);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
            ]);
        }
        
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    // Método para cargar el contenido del modal de detalles
    public function showModal($id)
    {
        $proveedor = $this->proveedorData->find($id);
        return view('proveedores.partials.show-modal', compact('proveedor'));
    }

    // Método para cargar el contenido del modal de editar
    public function editModal($id)
    {
        $proveedor = $this->proveedorData->find($id);
        $insumos = $this->insumoData->all(); // Obtener TODOS los insumos
        
        return view('proveedores.partials.edit-modal', compact('proveedor', 'insumos'));
    }
}