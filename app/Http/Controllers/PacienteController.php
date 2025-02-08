<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Nota;
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
    public function verNotas($id)
    {
        try {
            // Buscar el paciente y cargar sus notas ordenadas por fecha de creación (descendente)
            $paciente = Paciente::with(['notas' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Ordenar de más reciente a menos reciente
            }])->findOrFail($id);
    
            // Pasar los datos a la vista
            return view('pacientes.notas', compact('paciente'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si el paciente no existe, redirigir con un mensaje de error
            return redirect()->route('pacientes.index')->with('error', 'El paciente no existe.');
        } catch (\Exception $e) {
            Log::error("Error al obtener las notas del paciente: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al obtener las notas del paciente.');
        }
    }

public function notasData($id)
{
    try {
        // Obtener las notas del paciente como query (sin ejecutar todavía)
        $notas = Nota::where('paciente_id', $id);

        // Devolver los datos en el formato esperado por DataTables
        return DataTables::of($notas)
            ->editColumn('created_at', function ($nota) {
                return $nota->created_at ? $nota->created_at->format('d/m/Y H:i:s') : 'Sin fecha';
            })
            ->addColumn('acciones', function ($nota) {
                return '
                    <button class="btn btn-sm btn-info view-btn" data-id="' . $nota->id . '">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="' . $nota->id . '">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $nota->id . '">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['acciones']) // Para que las acciones se rendericen como HTML
            ->toJson();
    } catch (\Exception $e) {
        Log::error("Error al obtener notas: " . $e->getMessage());
        return response()->json(['error' => 'Error al obtener notas'], 500);
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