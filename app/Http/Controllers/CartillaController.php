<?php

namespace App\Http\Controllers;

use App\Models\Cartilla;
use App\Models\Puesto;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use App\Notifications\PagoRealizadoNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class CartillaController extends Controller
{
    /**
     * Muestra la cartilla de pagos de un puesto.
     */
    public function index($puesto_id)
    {
        $puesto = Puesto::with(['cliente', 'categoria'])->findOrFail($puesto_id);

        $cartillas = Cartilla::where('puesto_id', $puesto->id)
            ->orderBy('fecha_pagar', 'asc')
            ->get()
            ->groupBy(function ($cartilla) {
                return \Carbon\Carbon::parse($cartilla->fecha_pagar)->format('F Y');
            });

        return view('cartillas.index', compact('puesto', 'cartillas'));
    }

    /**
     * Genera una nueva cartilla automáticamente.
     */
    public function generar($puesto_id)
    {
        $puesto = Puesto::with(['cliente', 'categoria'])->findOrFail($puesto_id);

        // 🔹 Validar datos mínimos
        if (!$puesto->fecha_inicio || !$puesto->fecha_fin || !$puesto->primer_pago_monto) {
            return back()->with('error', 'El puesto no tiene configuradas fechas o monto inicial.');
        }

        // Borrar cartillas anteriores si las hubiera
        Cartilla::where('puesto_id', $puesto->id)->delete();

        // Configurar modo de pago (aunque aquí usamos solo jueves)
        $modo = $puesto->modo_pago ?? 'SEMANAL';

        // Convertimos fechas a objetos Carbon
        $fechaInicio = Carbon::parse($puesto->fecha_inicio);
        $fechaFin = Carbon::parse($puesto->fecha_fin);

        // 🔹 Buscar el primer jueves después (o igual) a la fecha de inicio
        if (!$fechaInicio->isThursday()) {
            $fechaInicio->next(Carbon::THURSDAY);
        }

        $nro = 1;

        // 🔹 Iterar por todos los jueves hasta la fecha fin
        while ($fechaInicio->lte($fechaFin)) {
            Cartilla::create([
                'puesto_id' => $puesto->id,
                'cliente_id' => $puesto->cliente_id,
                'nro' => $nro++,
                'fecha_pagar' => $fechaInicio->format('Y-m-d'),
                'cuota' => $puesto->primer_pago_monto, // monto semanal
                'observacion' => 'Pendiente',
                'modo_pago' => $modo,
                'accesor_cobro' => $puesto->accesor_cobro,
            ]);

            // Pasar al siguiente jueves
            $fechaInicio->addWeek();
        }

        return redirect()->route('cartillas.index', $puesto->id)
            ->with('success', 'Cartilla generada correctamente con pagos todos los jueves.');
    }


    /**
     * Marcar un pago como pagado o pendiente.
     */
    public function actualizarEstado(Cartilla $cartilla)
    {
        $cartilla->update([
            'observacion' => $cartilla->observacion === 'Pagado' ? 'Pendiente' : 'Pagado',
        ]);

        return back()->with('success', 'Estado de pago actualizado.');
    }

    public function listaGeneral()
    {
        $cartillas = Cartilla::with(['puesto', 'cliente'])
            ->orderBy('fecha_pagar', 'asc')
            ->get();

        return view('cartillas.lista', compact('cartillas'));
    }

    public function cartillasCliente($clienteId)
    {
        $cliente = Cliente::with(['puestos.cartillas'])->findOrFail($clienteId);
        $cartillas = $cliente->puestos->flatMap->cartillas;

        return view('cartillas.partials.tabla', compact('cartillas', 'cliente'));
    }

    public function cambiarEstado(Cartilla $cartilla, $estado)
    {
        $estadosPermitidos = ['Pagado', 'Pendiente', 'Pago Atrasado', 'No Pago'];

        if (!in_array($estado, $estadosPermitidos)) {
            return back()->with('error', 'Estado no válido.');
        }

        // Evitar modificar pagos ya cerrados
        if (in_array($cartilla->observacion, ['Pagado', 'Pago Atrasado'])) {
            return back()->with('warning', 'No se puede modificar un pago Pagado o Pago Atrasado.');
        }

        DB::beginTransaction();
        try {
            // Si es Pagado o Pago Atrasado → generar registro de pago
            if (in_array($estado, ['Pagado', 'Pago Atrasado'])) {
                // Obtener el último número correlativo
                $ultimo = Pago::select(DB::raw('MAX(CAST(SUBSTRING(numero_pago, 3) AS UNSIGNED)) as max_num'))
                    ->value('max_num');
                $numeroPago = $this->generarNumeroPago($ultimo);

                // Crear registro del pago
                $pago = Pago::create([
                    'numero_pago' => $numeroPago,
                    'fecha_pago' => now(),
                    'fecha_a_pagar' => $cartilla->fecha_pagar,
                    'monto' => $cartilla->cuota,
                    'estado' => 'PAGADO',
                    'cartilla_id' => $cartilla->id,
                ]);

                // Notificar al usuario (opcionalmente a todos los admins)
                //$usuarios = User::where('role', 'admin')->get(); // ajusta si tu modelo usa 'rol' o 'tipo'
                //Notification::send($usuarios, new PagoRealizadoNotification($pago));

                // Si quieres que también se notifique al usuario autenticado:
                if (auth()->check()) {
                    auth()->user()->notify(new PagoRealizadoNotification($pago));
                }

                // Actualizar la cartilla con el nuevo estado
                $cartilla->update(['observacion' => $estado]);

                DB::commit();

                // Generar el ticket
                return redirect()->route('cartillas.ticket', [
                    'cartillas' => $cartilla->id,
                    'pago' => $pago->id
                ]);
            }

            // Si no es un pago, solo actualiza el estado normal
            $cartilla->update(['observacion' => $estado]);
            DB::commit();

            return back()->with('success', "Estado actualizado a '{$estado}' correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir cartilla (PDF o vista limpia).
     */
    public function imprimir($puesto_id)
    {
        $puesto = Puesto::with(['cliente', 'categoria'])->findOrFail($puesto_id);
        $cartillas = Cartilla::where('puesto_id', $puesto->id)->get();

        return view('cartillas.imprimir', compact('puesto', 'cartillas'));
    }

    public function ingresarPagos(Request $request)
    {
        $ids = $request->input('cartillas_id', []);

        if (empty($ids)) {
            return back()->with('error', 'No seleccionaste ninguna cartilla.');
        }

        DB::beginTransaction();
        try {
            // Obtener el último número correlativo real
            $ultimo = Pago::select(DB::raw('MAX(CAST(SUBSTRING(numero_pago, 3) AS UNSIGNED)) as max_num'))
                        ->value('max_num');
            $numeroPago = $this->generarNumeroPago($ultimo);

            // Calcular el monto total
            $montoTotal = Cartilla::whereIn('id', $ids)->sum('cuota');

            // Crear registro del pago
            $pago = Pago::create([
                'numero_pago' => $numeroPago,
                'fecha_pago' => now(),
                'fecha_a_pagar' => now(),
                'monto' => $montoTotal,
                'estado' => 'PAGADO',
            ]);

            // Marcar las cartillas como pagadas
            Cartilla::whereIn('id', $ids)->update(['observacion' => 'Pagado']);

            // 🔔 Enviar notificación del pago múltiple
            if (auth()->check()) {
                auth()->user()->notify(new PagoRealizadoNotification($pago));
            }

            DB::commit();

            // Redirigir al ticket
            return redirect()->route('cartillas.ticket', [
                'cartillas' => implode(',', $ids),
                'pago' => $pago->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }


    /**
     * Genera un número correlativo tipo P-00001, P-00002...
     */
    private function generarNumeroPago($ultimo)
    {
        if (!$ultimo) {
            return 'P-00001';
        }

        $num = (int) filter_var($ultimo, FILTER_SANITIZE_NUMBER_INT);
        $nuevo = $num + 1;

        return 'P-' . str_pad($nuevo, 5, '0', STR_PAD_LEFT);
    }


    public function generarTicket($cartillaIds, Request $request)
    {
        $ids = explode(',', $cartillaIds);
        $pagoId = $request->query('pago');

        $cartillas = Cartilla::with(['cliente', 'puesto'])
            ->whereIn('id', $ids)
            ->get();

        if ($cartillas->isEmpty()) {
            return back()->with('error', 'No hay cartillas seleccionadas.');
        }

        $cliente = $cartillas->first()->cliente;
        $puestos = $cartillas->pluck('puesto.numero_puesto')->unique();
        $totalPago = $cartillas->sum('cuota');
        $fecha = now()->format('d/m/Y H:i');

        $pago = Pago::find($pagoId);
        $numeroPago = $pago ? $pago->numero_pago : '---';

        // Calcular alto del PDF dinámicamente
        // base: 250 (altura mínima) + 15 px adicionales por cada cartilla
        $alturaBase = 250; 
        $incrementoPorFila = 15; 
        $altura = $alturaBase + ($cartillas->count() * $incrementoPorFila);

        $pdf = Pdf::loadView('cartillas.ticket', compact('cartillas', 'cliente', 'puestos', 'totalPago', 'fecha', 'numeroPago'))
            ->setPaper([0, 0, 226.77, $altura], 'portrait');

        return $pdf->stream("ticket_pago_{$numeroPago}.pdf");
    }

}
