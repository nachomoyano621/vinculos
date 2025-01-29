<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class UserController extends Controller
{
  public function index()
{
    return view('users.index'); // Solo carga la vista
}

public function indexData()
{
    $users = User::select('id', 'name', 'email');
    return DataTables::of($users)->toJson();
}

public function store(Request $request)
{
    // Validación de datos
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $request->id,
        'password' => 'nullable|string|min:8',
    ]);

    // Si tiene un ID, es una actualización, si no, es una creación
    if ($request->id) {
        $user = User::find($request->id);
        $user->update($validated);
    } else {
        $user = User::create($validated);
    }

    return response()->json($user);
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'password' => 'nullable|string|min:8',
    ]);

    $user = User::findOrFail($id);
    $user->update($validated);

    return response()->json($user);
}

    public function show(User $user)
    {
        return response()->json($user);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);

    }

}
