<?php

namespace App\Http\Controllers;

use App\Models\CategoriaEstablecimiento;
use App\Models\Accesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriaEstablecimientoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaEstablecimiento::with('accesores')->orderBy('nombre')->paginate(15);
        $accesors = Accesor::orderBy('nombres')->get();

        return view('categorias.index', compact('categorias', 'accesors'));
    }

    public function create()
    {
        $accesors = Accesor::orderBy('nombres')->get();
        return view('categorias.create', compact('accesors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'imagen_lugar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'longitud_actual' => 'nullable|numeric',
            'latitud_actual' => 'nullable|numeric',
            'longitud_destino' => 'nullable|numeric',
            'latitud_destino' => 'nullable|numeric',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
            'pago_puesto' => 'required|numeric|min:0',
            'pago_inscripcion_anual' => 'required|numeric|min:0',
            'accesores' => 'array|nullable',
        ]);

        // Guardar imagen (si hay)
        if ($request->hasFile('imagen_lugar')) {
            $data['imagen_lugar'] = $request->file('imagen_lugar')->store('imagenes_lugares', 'public');
        }

        $categoria = CategoriaEstablecimiento::create($data);

        // Asignar personales (accesores)
        if (!empty($request->accesores)) {
            $categoria->accesores()->sync($request->accesores);
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function getInfo($id)
    {
        $categoria = CategoriaEstablecimiento::find($id);

        if (!$categoria) {
            return response()->json(['error' => 'Categoría no encontrada'], 404);
        }

        return response()->json([
            //'pago_puesto_contrato' => $categoria->pago_puesto,
            'pago_puesto' => $categoria->pago_puesto,
            'pago_inscripcion_anual' => $categoria->pago_inscripcion_anual,
            'hora_apertura' => $categoria->hora_apertura,
            'hora_cierre' => $categoria->hora_cierre,
        ]);
    }

    public function edit(CategoriaEstablecimiento $categoria)
    {
        $accesors = Accesor::orderBy('nombres')->get();
        return view('categorias.edit', compact('categoria', 'accesors'));
    }

    public function update(Request $request, CategoriaEstablecimiento $categoria)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'imagen_lugar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'longitud_actual' => 'nullable|numeric',
            'latitud_actual' => 'nullable|numeric',
            'longitud_destino' => 'nullable|numeric',
            'latitud_destino' => 'nullable|numeric',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
            'pago_puesto' => 'required|numeric|min:0',
            'pago_inscripcion_anual' => 'required|numeric|min:0',
            'accesores' => 'array|nullable',
        ]);

        if ($request->hasFile('imagen_lugar')) {
            if ($categoria->imagen_lugar && Storage::disk('public')->exists($categoria->imagen_lugar)) {
                Storage::disk('public')->delete($categoria->imagen_lugar);
            }
            $data['imagen_lugar'] = $request->file('imagen_lugar')->store('imagenes_lugares', 'public');
        }

        $categoria->update($data);

        $categoria->accesores()->sync($request->accesores ?? []);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function show(CategoriaEstablecimiento $categoria)
    {
        $categoria->load('accesores');
        return view('categorias.show', compact('categoria'));
    }

    public function destroy(CategoriaEstablecimiento $categoria)
    {
        if ($categoria->imagen_lugar && Storage::disk('public')->exists($categoria->imagen_lugar)) {
            Storage::disk('public')->delete($categoria->imagen_lugar);
        }

        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}