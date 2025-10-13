<?php

namespace App\Http\Controllers;

use App\Models\Cartilla;
use App\Models\Puesto;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // Si ya está pagado o atrasado, no se puede cambiar
        if (in_array($cartilla->observacion, ['Pagado', 'Pago Atrasado'])) {
            return back()->with('warning', 'No se puede modificar un pago Pagado o Pago Atrasado.');
        }

        // Si está "No Pago" solo puede ir a "Pago Atrasado"
        if ($cartilla->observacion === 'No Pago' && $estado !== 'Pago Atrasado') {
            return back()->with('warning', 'Un estado "No Pago" solo puede cambiar a "Pago Atrasado".');
        }

        $cartilla->update(['observacion' => $estado]);

        return back()->with('success', "Estado actualizado a '{$estado}' correctamente.");
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
    $ids = $request->input('cartillas', []);
    if (empty($ids)) {
        return back()->with('error', 'No seleccionaste ninguna cartilla.');
    }

    Cartilla::whereIn('id', $ids)->update(['observacion' => 'Pagado']);

    return back()->with('success', 'Pagos marcados como pagados correctamente.');
}

}
