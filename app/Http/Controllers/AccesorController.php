<?php

namespace App\Http\Controllers;

use App\Models\Accesor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccesorController extends Controller
{
    public function index()
    {
        $accesores = Accesor::orderBy('nombres')->paginate(15);
        return view('accesores.index', compact('accesores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:9',
            'dni' => 'required|string|size:8|unique:accesors,dni',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // confirmado con password_confirmation
        ]);

        // Crear usuario vinculado
        $user = User::create([
            'name' => $data['nombres'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Crear accesor vinculado al usuario
        Accesor::create([
            'nombres' => $data['nombres'],
            'direccion' => $data['direccion'],
            'celular' => $data['celular'],
            'dni' => $data['dni'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('accesores.index')->with('success', 'Accesor creado con su usuario correctamente.');
    }

    public function update(Request $request, Accesor $accesor)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:9',
            'dni' => [
                'required',
                'string',
                'size:8',
                Rule::unique('accesors', 'dni')->ignore($accesor->id),
            ],
        ]);

        // Evita actualizar con datos idénticos a otro registro existente
        $existe = Accesor::where('nombres', $data['nombres'])
                         ->where('dni', $data['dni'])
                         ->where('id', '!=', $accesor->id)
                         ->first();

        if ($existe) {
            return redirect()->back()->with('error', 'Ya existe otro accesor con esos mismos datos.');
        }

        $accesor->update($data);

        return redirect()->route('accesores.index')->with('success', 'Accesor actualizado correctamente.');
    }

    public function destroy(Accesor $accesor)
    {
        $accesor->delete();
        return redirect()->route('accesores.index')->with('success', 'Accesor eliminado.');
    }
}