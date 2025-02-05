<?php

namespace App\Http\Controllers;

use App\Models\OSocial;
use Illuminate\Http\Request;
use DataTables;
use Log;

class OSocialController extends Controller
{
    // Vista principal de obras sociales
    public function index()
    {
        return view('osocial.index');
    }

    // Obtener datos para DataTables
    public function indexData()
{
    try {
        $osocial = OSocial::select('id', 'nombre'); // Asegúrate de que la consulta sea correcta
        return DataTables::of($osocial)->toJson();
    } catch (\Exception $e) {
        Log::error("Error al obtener datos de obras sociales: " . $e->getMessage());
        return response()->json(['error' => 'Error al obtener datos de obras sociales'], 500);
    }
}

    // Validaciones reutilizables
    private function getValidationRules($id = null)
    {
        return [
            'nombre' => 'required|string|max:255|unique:osocial,nombre,' . $id,
        ];
    }

    private function getValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe una obra social con este nombre.',
        ];
    }

    // Crear o actualizar una obra social
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($request->id),
                $this->getValidationMessages()
            );

            $osocial = $request->id ? OSocial::findOrFail($request->id) : new OSocial();
            $osocial->fill($validated)->save();

            return response()->json($osocial);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar obra social: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar obra social'], 500);
        }
    }

    // Actualizar una obra social por ID
    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    // Mostrar una obra social específica
    public function show(OSocial $osocial)
    {
        try {
            return response()->json($osocial);
        } catch (\Exception $e) {
            Log::error("Error al obtener obra social: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener obra social'], 500);
        }
    }

    // Eliminar una obra social (Soft Delete)
    public function destroy(OSocial $osocial)
    {
        try {
            $osocial->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar obra social: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar obra social'], 500);
        }
    }
    // Conteo de usuarios
    public function count()
    {
        try {
            $count = OSocial::count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error al obtener el conteo de osocial: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }
    public function getAll()
{
    try {
        $osocials = OSocial::select('id', 'nombre')->get();
        return response()->json($osocials);
    } catch (\Exception $e) {
        Log::error("Error al obtener obras sociales: " . $e->getMessage());
        return response()->json(['error' => 'Error al obtener obras sociales'], 500);
    }
}
}