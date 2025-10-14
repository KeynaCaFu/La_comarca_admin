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
        return view('proveedor.index', compact('proveedores'));
    }

    public function show($id)
    {
        $proveedor = $this->proveedorData->find($id);
        if (! $proveedor) {
            return redirect()->route('proveedor.index')->with('warning', 'Proveedor no encontrado.');
        }
        return response()->json($proveedor);
    }

    public function create()
    {
        $insumos = $this->insumoData->all(); // Obtener TODOS los insumos
        return view('proveedor.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
        ]);

        $id = $this->proveedorData->create($data);

        return redirect()->route('proveedor.index')->with('success', 'Proveedor creado.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
        ]);

        $this->proveedorData->update($id, $data);

        return redirect()->route('proveedor.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy($id)
    {
        $this->proveedorData->delete($id);
        return redirect()->route('proveedor.index')->with('success', 'Proveedor eliminado.');
    }

    // Método para cargar el contenido del modal de detalles
    public function showModal($id)
    {
        $proveedor = $this->proveedorData->find($id);
        return view('proveedor.partials.show-modal', compact('proveedor'));
    }

    // Método para cargar el contenido del modal de editar
    public function editModal($id)
    {
        $proveedor = $this->proveedorData->find($id);
        $insumos = $this->insumoData->all(); // Obtener TODOS los insumos
        
        return view('proveedor.partials.edit-modal', compact('proveedor', 'insumos'));
    }
}