<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplierData;
use App\Data\SupplyData;

class SupplierController extends Controller
{
    protected $supplierData;
    protected $supplyData;

    public function __construct(SupplierData $supplierData, SupplyData $supplyData)
    {
        $this->supplierData = $supplierData;
        $this->supplyData = $supplyData;
    }

    /**
     * Mostrar lista de suppliers con filtros
     */
    public function index(Request $request)
    {
        // Mapear nombres de filtros de español a inglés
        $filters = [
            'search' => $request->input('buscar'), // buscar → search
            'status' => $this->mapStatusToEnglish($request->input('estado')) // estado → status
        ];

        $suppliers = $this->supplierData->all($filters);
        $totals = $this->supplierData->countTotals();
        $supplies = $this->supplyData->all();

        return view('suppliers.index', compact('suppliers', 'totals', 'supplies'));
    }

    /**
     * Mostrar detalles de un supplier
     */
    public function show($id)
    {
        $supplier = $this->supplierData->find($id);
        
        if (!$supplier) {
            return redirect()->route('suppliers.index')
                ->with('warning', 'Proveedor no encontrado.');
        }
        
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Crear nuevo supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'required|string'
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'] ?? null,
            'email' => $validated['correo'] ?? null,
            'address' => $validated['direccion'] ?? null,
            'total_purchases' => $validated['total_compras'] ?? 0,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $supplies = $request->input('insumos', []);
        $id = $this->supplierData->create($data, $supplies);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado exitosamente',
                'id' => $id
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    /**
     * Actualizar supplier existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'required|string'
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'] ?? null,
            'email' => $validated['correo'] ?? null,
            'address' => $validated['direccion'] ?? null,
            'total_purchases' => $validated['total_compras'] ?? 0,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $supplies = $request->input('insumos', null);
        $this->supplierData->update($id, $data, $supplies);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado exitosamente'
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Eliminar supplier
     */
    public function destroy($id)
    {
        $this->supplierData->delete($id);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
            ]);
        }
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }

    /**
     * Cargar contenido del modal de detalles
     */
    public function showModal($id)
    {
        $supplier = $this->supplierData->find($id);
        
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        return view('suppliers.partials.show-modal', compact('supplier'));
    }

    /**
     * Cargar contenido del modal de editar
     */
    public function editModal($id)
    {
        $supplier = $this->supplierData->find($id);
        
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        $supplies = $this->supplyData->all();
        
        return view('suppliers.partials.edit-modal', compact('supplier', 'supplies'));
    }

    /**
     * Mapear estado de español a inglés
     */
    private function mapStatusToEnglish($status)
    {
        if (!$status) return null;
        
        $statusMap = [
            'Activo' => 'Active',
            'Inactivo' => 'Inactive'
        ];
        
        return $statusMap[$status] ?? $status;
    }
}
