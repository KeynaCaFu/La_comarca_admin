<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplierData;
use App\Data\SupplyData;
use Illuminate\Support\Facades\URL;

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
        // Solo cargar nombre e ID de supplies para el modal de crear
        $supplies = $this->supplyData->allMinimal();

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

        // Generar URL firmada temporal para restaurar (10 segundos)
        $restoreUrl = URL::temporarySignedRoute('suppliers.restore', now()->addSeconds(10), ['id' => $id]);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente',
                'restore_url' => $restoreUrl
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente')
            ->with('restore_url', $restoreUrl);
    }

    /**
     * Restaurar supplier eliminado
     */
    public function restore(Request $request, $id)
    {
        // La ruta estará protegida por middleware 'signed'
        $supplier = $this->supplierData->restore($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor restaurado correctamente',
                'supplier_id' => $supplier->supplier_id,
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor restaurado correctamente');
    }

    /**
     * Cargar contenido del modal de detalles
     */
    public function showModal($id)
    {
        // Solo cargar el supplier con datos mínimos de supplies
        $supplier = $this->supplierData->findForModal($id);
        
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
        // Solo cargar el supplier con IDs de supplies
        $supplier = $this->supplierData->findForEdit($id);
        
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        // Solo cargar nombre e ID de supplies (no todos sus datos)
        $supplies = $this->supplyData->allMinimal();
        
        return view('suppliers.partials.edit-modal', compact('supplier', 'supplies'));
    }

    /**
     * Verificar si un email ya está registrado
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $supplierId = $request->input('supplier_id');
        
        $query = $this->supplierData->getModel()->where('email', $email);
        
        // Si estamos editando, excluir el proveedor actual
        if ($supplierId) {
            $query->where('supplier_id', '!=', $supplierId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este correo ya está registrado' : 'Correo disponible'
        ]);
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
