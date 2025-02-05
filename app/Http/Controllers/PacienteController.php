<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\OSocial;
use Illuminate\Http\Request;
use DataTables;
use Log;

class PacienteController extends Controller
{
    // Vista principal de pacientes
    public function index()
    {
        return view('pacientes.index');
    }

    // Obtener datos para DataTables
    public function indexData()
    {
        try {
            $pacientes = Paciente::with('osocial')->select('pacientes.*');
            return DataTables::of($pacientes)
                ->addColumn('osocial_nombre', function ($paciente) {
                    return $paciente->osocial ? $paciente->osocial->nombre : 'Sin obra social';
                })
                ->toJson();
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de pacientes: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos de pacientes'], 500);
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
            'osocial_id' => 'required|exists:osocial,id',
        ];
    }

    private function getValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'osocial_id.required' => 'La obra social es obligatoria.',
            'osocial_id.exists' => 'La obra social seleccionada no existe.',
        ];
    }

    // Crear o actualizar un paciente
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($request->id),
                $this->getValidationMessages()
            );

            $paciente = $request->id ? Paciente::findOrFail($request->id) : new Paciente();
            $paciente->fill($validated)->save();

            return response()->json($paciente);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar paciente: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar paciente'], 500);
        }
    }

    // Actualizar un paciente por ID
    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    // Mostrar un paciente específico
    public function show(Paciente $paciente)
    {
        try {
            return response()->json($paciente);
        } catch (\Exception $e) {
            Log::error("Error al obtener paciente: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener paciente'], 500);
        }
    }

    // Eliminar un paciente
    public function destroy(Paciente $paciente)
    {
        try {
            $paciente->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar paciente: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar paciente'], 500);
        }
    }
     // Conteo de usuarios
     public function count()
     {
         try {
             $count = Paciente::count();
             return response()->json(['count' => $count]);
         } catch (\Exception $e) {
             Log::error('Error al obtener el conteo de osocial: ' . $e->getMessage());
             return response()->json(['count' => 0]);
         }
     }
}