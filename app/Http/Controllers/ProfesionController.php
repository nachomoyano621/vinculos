<?php

namespace App\Http\Controllers;

use App\Models\Profesion;
use Illuminate\Http\Request;
use DataTables;
use Log;

class ProfesionController extends Controller
{
    // Vista principal de profesiones
    public function index()
    {
        return view('profesiones.index');
    }

    // Obtener datos para DataTables
    public function indexData()
    {
        try {
            $profesiones = Profesion::select('id', 'nombre', 'created_at'); // Selecciona los campos necesarios
            return DataTables::of($profesiones)
                ->editColumn('created_at', function ($profesion) {
                    // Formatear la fecha como DD/MM/AAAA HH:MM
                    $date = new \DateTime($profesion->created_at);
                    return $date->format('d/m/Y H:i');
                })
                ->toJson();
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de profesiones: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos de profesiones'], 500);
        }
    }

    // Validaciones reutilizables
    private function getValidationRules($id = null)
    {
        return [
            'nombre' => 'required|string|max:1000|unique:profesiones,nombre,' . $id,
        ];
    }

    private function getValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 1000 caracteres.',
            'nombre.unique' => 'Ya existe una profesión con este nombre.',
        ];
    }

    // Crear o actualizar una profesión
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($request->id),
                $this->getValidationMessages()
            );

            $profesion = $request->id ? Profesion::findOrFail($request->id) : new Profesion();
            $profesion->fill($validated)->save();

            return response()->json($profesion);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar profesión: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar profesión'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Buscar la profesión por ID
        $profesion = Profesion::findOrFail($id);
    
        // Validación de los datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255', // Asegúrate de agregar las reglas de validación necesarias
        ]);
    
        // Actualizar los campos
        $profesion->nombre = $validated['nombre'];
    
        // Guardar los cambios
        $profesion->save();
    
        return response()->json(['message' => 'Profesión actualizada correctamente', 'profesion' => $profesion]);
    }
    

   // Mostrar una profesión específica
   public function show($id) // Recibe el id, no la profesión como parámetro
   {
       try {
           $profesion = Profesion::findOrFail($id);
           return response()->json($profesion);
       } catch (\Exception $e) {
           Log::error("Error al obtener profesión: " . $e->getMessage());
           return response()->json(['error' => 'Error al obtener profesión'], 500);
       }
   }
   

    // Eliminar una profesión (Soft Delete)
    public function destroy($id)
    {
        try {
            $profesion = Profesion::findOrFail($id);
            $profesion->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar profesión: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar profesión'], 500);
        }
    }
    
   

    // Obtener todas las profesiones
    public function getAll()
    {
        try {
            $profesiones = Profesion::select('id', 'nombre')->get();
            return response()->json($profesiones);
        } catch (\Exception $e) {
            Log::error("Error al obtener profesiones: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener profesiones'], 500);
        }
    }
}