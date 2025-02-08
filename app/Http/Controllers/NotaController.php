<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotaController extends Controller
{
    // Mostrar todas las notas
    public function index()
    {
        try {
            $notas = Nota::with('paciente')->get();
            return response()->json($notas);
        } catch (\Exception $e) {
            Log::error("Error al obtener notas: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener notas'], 500);
        }
    }

    // Mostrar una nota específica
    public function show($id)
    {
        try {
            $nota = Nota::with('paciente')->findOrFail($id);
            return response()->json($nota);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Nota no encontrada'], 404);
        }
    }

    // Crear una nueva nota
    public function store(Request $request)
    {
        try {
            // Validar los datos
            $validated = $request->validate([
                'paciente_id' => 'required|exists:pacientes,id',
                'nombre' => 'required|string|max:1000|min:10',
            ]);

            // Crear la nota con el ID del usuario autenticado
            $nota = Nota::create([
                'paciente_id' => $validated['paciente_id'],
                'nombre' => $validated['nombre'],
                'usuario_registro' => auth()->id(), // ID del usuario autenticado
            ]);

            return response()->json($nota, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al crear nota: " . $e->getMessage());
            return response()->json(['error' => 'Error al crear nota'], 500);
        }
    }

    // Actualizar una nota
    public function update(Request $request, $id)
    {
        try {
            $nota = Nota::findOrFail($id);

            // Validar los datos
            $validated = $request->validate([
                'nombre' => 'sometimes|required|string|max:1000|min:10',
            ]);

            // Actualizar la nota
            $nota->update($validated);

            return response()->json($nota);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al actualizar nota: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar nota'], 500);
        }
    }

    // Eliminar una nota
    public function destroy($id)
    {
        try {
            $nota = Nota::findOrFail($id);
            $nota->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar nota: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar nota'], 500);
        }
    }
}