<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\Profesion;
use Illuminate\Http\Request;
use DataTables;
use Log;

class ProfesionalController extends Controller
{
    // Vista principal de profesionales
    public function index()
    {
        return view('profesionales.index');
    }

    // Obtener datos para DataTables
    public function indexData()
    {
        try {
            // Solo obtenemos profesionales que no han sido soft deleted
            $profesionales = Profesional::whereNull('deleted_at')->select('profesionales.*');
            return DataTables::of($profesionales)
                ->addColumn('profesion', function ($profesional) {
                    return $profesional->profesion ? $profesional->profesion->nombre : 'Sin profesión';
                })
                ->toJson();
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de profesionales: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos de profesionales'], 500);
        }
    }

    // Validaciones reutilizables
    private function getValidationRules($id = null)
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cuil' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono1' => 'nullable|string|max:255',
            'telefono2' => 'nullable|string|max:255',
            'nrosocial' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'profesion_id' => 'required|exists:profesiones,id', // Asegúrate de tener la relación con profesiones
        ];
    }

    private function getValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'profesion_id.required' => 'Debe seleccionar una profesión.',
            'profesion_id.exists' => 'La profesión seleccionada no existe.',
        ];
    }

    // Crear o actualizar un profesional
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($request->id),
                $this->getValidationMessages()
            );

            $profesional = $request->id ? Profesional::findOrFail($request->id) : new Profesional();
            $profesional->fill($validated);
            $profesional->save();

            
            return response()->json($profesional);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar profesional: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar profesional'], 500);
        }
    }

    // Actualizar un profesional por ID
    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    // Mostrar un profesional específico
    public function show(Profesional $profesional)
    {
        try {
            return response()->json($profesional);
        } catch (\Exception $e) {
            Log::error("Error al obtener profesional: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener profesional'], 500);
        }
    }

    // Eliminar un profesional (Soft Delete)
    public function destroy(Profesional $profesional)
    {
        try {
            $profesional->delete(); // Realiza un soft delete
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar profesional: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar profesional'], 500);
        }
    }
    // app/Http/Controllers/ProfesionController.php
 public function getAll()
{
    $profesiones = Profesion::all(); // O el método que corresponda para obtener las profesiones
    return response()->json($profesiones);
}
public function create()
{
    try {
        // Obtener la lista de profesiones para asociar al profesional
        $profesiones = Profesion::all();

        // Pasar los datos a la vista
        return view('profesionales.create', compact('profesiones'));
    } catch (\Exception $e) {
        Log::error("Error al cargar el formulario de creación de profesionales: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error al cargar el formulario de creación de profesionales.');
    }
}

}
