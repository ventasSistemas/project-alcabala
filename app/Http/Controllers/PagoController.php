<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Log;
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

        if ($accesor) {
            $cartillasIds = \App\Models\Cartilla::whereHas('puesto.accesores', function ($q) use ($accesor) {
                $q->where('accesor_id', $accesor->id);
            })->pluck('id')->toArray();

            $pagos = Pago::where(function ($q) use ($cartillasIds, $accesor) {
                    $q->whereIn('cartilla_id', $cartillasIds)
                    ->orWhere('accesor_id', $accesor->id);
                })
                ->with(['cartilla.puesto', 'cartilla.cliente'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $pagos = Pago::with(['cartilla.puesto', 'cartilla.cliente', 'accesor'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Total pendiente de enviar a caja
        $totalPendiente = $pagos->where('enviado_a_caja', false)->sum('monto');

        return view('pagos.index', compact('pagos', 'totalPendiente'));
    }


    public function enviarTodoCaja()
    {
        $user = auth()->user();
        $accesor = $user->accesor;

        // Buscar la caja abierta del usuario actual
        $caja = Caja::where('user_id', $user->id)
                    ->where('estado', 'ABIERTA')
                    ->first();

        if (!$caja) {
            return back()->with('error', 'No tienes una caja abierta. Abre una para enviar los pagos.');
        }

        // Traer todos los pagos no enviados (individuales o múltiples)
        $pagos = Pago::where('estado', 'PAGADO')
                    ->where('enviado_a_caja', false)
                    ->when($accesor, function ($q) use ($accesor) {
                        // Incluir tanto los pagos de cartillas como los múltiples (cartilla_id = null)
                        $q->where(function ($sub) use ($accesor) {
                            $sub->whereHas('cartilla.puesto.accesores', function ($query) use ($accesor) {
                                    $query->where('accesor_id', $accesor->id);
                                })
                                ->orWhere('accesor_id', $accesor->id);
                        });
                    })
                    ->get();

        if ($pagos->isEmpty()) {
            return back()->with('warning', 'No hay pagos nuevos para enviar a caja.');
        }

        try {
            // Calcular total
            $total = $pagos->sum('monto');

            // Crear movimiento de ingreso
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'tipo' => 'INGRESO',
                'descripcion' => 'Ingreso total por pagos individuales y múltiples (Historial de Pagos/Caja)',
                'monto' => $total,
            ]);

            // Actualizar caja
            $caja->increment('total_ingresos', $total);
            $caja->increment('saldo_final', $total);

            // Marcar pagos como enviados
            Pago::whereIn('id', $pagos->pluck('id'))->update(['enviado_a_caja' => true]);

            return back()->with('success', 'Todos los pagos (individuales y múltiples) fueron enviados correctamente a la caja.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar los pagos: ' . $e->getMessage());
        }
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
