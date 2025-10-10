<?php

namespace App\Http\Controllers;

use App\Models\Accesor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        ]);

        // Evita crear duplicados exactos (por nombres y dni)
        $existe = Accesor::where('nombres', $data['nombres'])
                         ->where('dni', $data['dni'])
                         ->first();

        if ($existe) {
            return redirect()->back()->with('error', 'El accesor ya existe con esos mismos datos.');
        }

        Accesor::create($data);

        return redirect()->route('accesores.index')->with('success', 'Accesor creado correctamente.');
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