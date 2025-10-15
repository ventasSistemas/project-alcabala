<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Cartilla;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Muestra el historial de pagos
     */
    public function index()
    {
        $user = auth()->user();
        $accesor = $user->accesor;

        // Si el usuario tiene un accesor asociado → solo sus cobros
        if ($accesor) {
            $pagos = Pago::whereHas('cartilla.puesto.accesores', function ($q) use ($accesor) {
                $q->where('accesor_id', $accesor->id);
            })
            ->with(['cartilla.puesto', 'cartilla.cliente'])
            ->orderBy('created_at', 'desc')
            ->get();
        } else {
            // 🔹 Si NO tiene accesor → mostrar todos los pagos
            $pagos = Pago::with(['cartilla.puesto', 'cartilla.cliente'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pagos.index', compact('pagos'));
    }

    /**
     * Muestra el detalle de un pago específico
     */
    public function show($id)
    {
        $pago = Pago::with(['cartilla.puesto', 'cartilla.cliente'])->findOrFail($id);
        return view('pagos.show', compact('pago'));
    }
}
