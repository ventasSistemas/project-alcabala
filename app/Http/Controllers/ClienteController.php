<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('dni')) {
            $query->where('dni', 'like', "%{$request->dni}%");
        }

        if ($request->filled('nombres')) {
            $query->whereRaw("CONCAT(nombres) LIKE ?", ["%{$request->nombres}%"]);
        }

        $clientesCartillas = Cliente::with(['puestos.cartillas'])->paginate(10);

        $clientes = $query->orderBy('nombres')->paginate(20);
        return view('clientes.index', compact('clientes', 'clientesCartillas'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            //'apellidos' => 'nullable|string|max:255',
            'dni' => 'required|string|size:8|unique:clientes,dni',
            'celular' => 'nullable|string|max:9',
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado.');
    }

    public function buscar(Request $request)
    {
        $q = $request->query('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::where('dni', 'like', "%{$q}%")
            ->orWhere('nombres', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'dni', 'nombres']);

        return response()->json($clientes);
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            //'apellidos' => 'nullable|string|max:255',
            'dni' => 'required|string|size:8|unique:clientes,dni,' . $cliente->id,
            'celular' => 'nullable|string|max:9',
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['contratos.pagos', 'puestos']);
        return view('clientes.show', compact('cliente'));
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }
}