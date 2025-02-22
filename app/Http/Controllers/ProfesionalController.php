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
            $profesionales = Profesional::with('profesion')->select('profesionales.*');
            return DataTables::of($profesionales)
                ->addColumn('profesion_nombre', function ($profesional) {
                    return $profesional->profesion ? $profesional->profesion->nombre : 'Sin profesión';
                })
                ->toJson();
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de profesionales: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos de profesionales'], 500);
        }
    }

    public function update(Request $request, $id)
{
    try {
        // Validación de los datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'profesion_id' => 'required|exists:profesiones,id',
            'cuil' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono1' => 'nullable|string|max:255',
            'telefono2' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        // Buscar el profesional por ID
        $profesional = Profesional::findOrFail($id);

        // Actualizar los campos
        $profesional->fill($validated)->save();

        return response()->json(['message' => 'Profesional actualizado correctamente', 'profesional' => $profesional]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Error en la validación',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error("Error al actualizar profesional: " . $e->getMessage());
        return response()->json(['error' => 'Error al actualizar profesional'], 500);
    }
}
    // Crear o actualizar un profesional
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'profesion_id' => 'required|exists:profesiones,id',
                'cuil' => 'nullable|string|max:255',
                'dni' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'telefono1' => 'nullable|string|max:255',
                'telefono2' => 'nullable|string|max:255',
                'observaciones' => 'nullable|string',
            ]);

            $profesional = $request->id ? Profesional::findOrFail($request->id) : new Profesional();
            $profesional->fill($validated)->save();

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

    // Mostrar un profesional específico
    public function show($id)
    {
        try {
          
    
            // Depuración: Verificar si el profesional existe
            $profesional = Profesional::with('profesion')->find($id);
    
            if (!$profesional) {
                Log::warning("Profesional con ID {$id} no encontrado");
                return response()->json(['error' => 'Profesional no encontrado'], 404);
            }
    
            // Depuración: Verificar la relación con la profesión
            if (!$profesional->profesion) {
                Log::warning("Profesional con ID {$id} no tiene una profesión asociada");
            }
    
            return response()->json($profesional);
        } catch (\Exception $e) {
            Log::error("Error al obtener profesional: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los detalles del profesional'], 500);
        }
    }


    // Eliminar un profesional (Soft Delete)
    public function destroy($id)
    {
        try {
            $profesional = Profesional::findOrFail($id);
            $profesional->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar profesional: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar profesional'], 500);
        }
    }
}