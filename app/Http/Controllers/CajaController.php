<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\User;
use App\Notifications\CajaCerradaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    /**
     * Listar todas las cajas.
     */
    public function index()
    {
        $cajas = Caja::with(['user', 'accesor'])->latest()->get();
        return view('cajas.index', compact('cajas'));
    }

    /**
     * Formulario para abrir una nueva caja.
     */
    public function create()
    {
        return view('cajas.create');
    }

    /**
     * Abrir una nueva caja.
     */
    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $caja = Caja::create([
            'user_id' => Auth::id(), 
            'monto_inicial' => $request->monto_inicial,
            'total_ingresos' => 0,
            'total_egresos' => 0,
            'saldo_final' => $request->monto_inicial,
            'estado' => 'ABIERTA',
            'fecha_apertura' => now()->toDateString(),
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja abierta correctamente.');
    }



    /**
     * Ver detalles de una caja.
     */
    public function show($id)
    {
        $caja = Caja::with('movimientos')->findOrFail($id);
        return view('cajas.show', compact('caja'));
    }

    /**
     * Cerrar la caja.
     */
    public function cerrar($id)
    {
        $caja = Caja::findOrFail($id);

        if ($caja->estado === 'CERRADA') {
            return back()->with('warning', 'Esta caja ya está cerrada.');
        }

        $caja->update([
            'estado' => 'CERRADA',
            'fecha_cierre' => now(),
        ]);

        // Notificar al admin
        $admin = User::where('email', 'admin@cleanwash.com')->first(); // o rol admin
        if ($admin) {
            $admin->notify(new CajaCerradaNotification($caja));
        }

        return back()->with('success', 'Caja cerrada correctamente y se notificó al administrador.');
    }

    /**
     * Eliminar una caja (si está vacía o cerrada).
     */
    public function destroy($id)
    {
        $caja = Caja::findOrFail($id);

        if ($caja->estado === 'ABIERTA') {
            return back()->with('error', 'No puedes eliminar una caja abierta.');
        }

        $caja->delete();

        return back()->with('success', 'Caja eliminada correctamente.');
    }
}
