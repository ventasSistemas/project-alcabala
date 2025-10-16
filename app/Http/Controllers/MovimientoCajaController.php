<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\Caja;
use Illuminate\Http\Request;

class MovimientoCajaController extends Controller
{
    /**
     * 📋 Listar movimientos.
     */
    public function index()
    {
        $movimientos = MovimientoCaja::with('caja')->latest()->get();
        return view('movimientos.index', compact('movimientos'));
    }

    /**
     * ➕ Crear nuevo movimiento (vista).
     */
    public function create()
    {
        $cajas = Caja::where('estado', 'ABIERTA')->get();
        return view('movimientos.create', compact('cajas'));
    }

    /**
     * 💰 Registrar movimiento (INGRESO o EGRESO).
     */
    public function store(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'tipo' => 'required|in:INGRESO,EGRESO',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
        ]);

        $caja = Caja::findOrFail($request->caja_id);

        if ($caja->estado === 'CERRADA') {
            return back()->with('error', 'No se pueden registrar movimientos en una caja cerrada.');
        }

        // Registrar el movimiento
        MovimientoCaja::create($request->all());

        // Actualizar los totales de la caja
        if ($request->tipo === 'INGRESO') {
            $caja->increment('total_ingresos', $request->monto);
            $caja->increment('saldo_final', $request->monto);
        } else {
            $caja->increment('total_egresos', $request->monto);
            $caja->decrement('saldo_final', $request->monto);
        }

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * 🔍 Ver detalle de movimiento.
     */
    public function show($id)
    {
        $movimiento = MovimientoCaja::with('caja')->findOrFail($id);
        return view('movimientos.show', compact('movimiento'));
    }

    /**
     * ❌ Eliminar movimiento.
     */
    public function destroy($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $caja = $movimiento->caja;

        if ($caja->estado === 'CERRADA') {
            return back()->with('error', 'No se pueden eliminar movimientos de una caja cerrada.');
        }

        // Revertir el movimiento
        if ($movimiento->tipo === 'INGRESO') {
            $caja->decrement('total_ingresos', $movimiento->monto);
            $caja->decrement('saldo_final', $movimiento->monto);
        } else {
            $caja->decrement('total_egresos', $movimiento->monto);
            $caja->increment('saldo_final', $movimiento->monto);
        }

        $movimiento->delete();

        return back()->with('success', 'Movimiento eliminado correctamente.');
    }
}