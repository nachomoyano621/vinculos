<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index'); // Solo carga la vista
    }

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

    public function store(Request $request)
    {
        try {
            // Validación de datos con mensajes personalizados
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $request->id,
                'password' => 'nullable|string|min:8',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre debe ser una cadena de texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo debe ser una dirección de correo electrónico válida.',
                'email.max' => 'El correo no puede tener más de 255 caracteres.',
                'email.unique' => 'Ya existe un usuario con este correo.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);

            // Si tiene un ID, es una actualización, si no, es una creación
            if ($request->id) {
                $user = User::find($request->id);
                $user->update($validated);
            } else {
                $user = User::create($validated);
            }

            return response()->json($user);
        } catch (ValidationException $e) {
            // Si ocurre un error de validación, puedes personalizar el mensaje
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(), // Devuelve los errores de validación
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar/actualizar usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar/actualizar usuario'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validación de datos con mensajes personalizados
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre debe ser una cadena de texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo debe ser una dirección de correo electrónico válida.',
                'email.max' => 'El correo no puede tener más de 255 caracteres.',
                'email.unique' => 'Ya existe un usuario con este correo.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);

            $user = User::findOrFail($id);
            $user->update($validated);

            return response()->json($user);
        } catch (ValidationException $e) {
            // Si ocurre un error de validación, puedes personalizar el mensaje
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(), // Devuelve los errores de validación
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al actualizar usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar usuario'], 500);
        }
    }

    public function show(User $user)
    {
        try {
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error("Error al obtener usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener usuario'], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }
}
