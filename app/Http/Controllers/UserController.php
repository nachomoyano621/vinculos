<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Log;
use App\Models\OSocial;
use App\Models\Paciente;
use App\Models\Profesion;


class UserController extends Controller
{
    // Vista principal de usuarios
    public function index()
    {
        return view('users.index');
    }

    // Obtener datos para DataTables
    public function indexData()
    {
        try {
            $users = User::select('id', 'name', 'email');
            return DataTables::of($users)->toJson();
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de usuarios: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos de usuarios'], 500);
        }
    }

    // Validaciones reutilizables
    private function getValidationRules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ];
    }

    private function getValidationMessages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser una dirección de correo electrónico válida.',
            'email.max' => 'El correo no puede tener más de 255 caracteres.',
            'email.unique' => 'Ya existe un usuario con este correo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }

    // Crear o actualizar un usuario
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($request->id),
                $this->getValidationMessages()
            );

            $user = $request->id ? User::findOrFail($request->id) : new User();
            $user->fill($validated)->save();

            return response()->json($user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar usuario'], 500);
        }
    }

    // Actualizar un usuario por ID
    public function update(Request $request, $id)
    {
        // Agregar el ID del usuario a la solicitud para que la validación funcione correctamente
        $request->merge(['id' => $id]);
        return $this->store($request); // Reutiliza el método store
    }

    // Mostrar un usuario específico
    public function show(User $user)
    {
        try {
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error("Error al obtener usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener usuario'], 500);
        }
    }

    // Eliminar un usuario
    public function destroy(User $user)
    {
        try {
            $user->delete(); // Esto realiza una eliminación lógica (soft delete)
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }

    public function getCounts()
    {
        try {
            $userCount = User::count();
            $osocialCount = OSocial::count();
            $pacienteCount = Paciente::count();
            $profesionCount = Profesion::count();
    
            return response()->json([
                'userCount' => $userCount,
                'osocialCount' => $osocialCount,
                'pacienteCount' => $pacienteCount,
                'profesionCount' => $profesionCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener los conteos: ' . $e->getMessage());
            return response()->json([
                'userCount' => 0,
                'osocialCount' => 0,
                'pacienteCount' => 0,
                'profesionCount' => 0
            ]);
        }
    }
    
}