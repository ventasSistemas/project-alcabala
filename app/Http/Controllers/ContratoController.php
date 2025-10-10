<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with('cliente','puesto')->orderBy('fecha_inicio','desc')->paginate(20);
        return view('contratos.index', compact('contratos'));
    }

    /**
     * Store usado cuando se crea contrato desde formulario normal.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'puesto_id' => 'required|exists:puestos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'frecuencia_pago' => 'required|in:SEMANAL,MENSUAL,ANUAL',
            'monto' => 'required|numeric|min:0',
            'renovable' => 'sometimes|boolean',
        ]);

        return $this->storeFromAssignment($data);
    }

    /**
     * Método reutilizable para crear contrato desde otros controladores (asignación de puestos).
     * Acepta array $data con las mismas keys que arriba.
     */
    public function storeFromAssignment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $contrato = Contrato::create([
                'cliente_id' => $data['cliente_id'],
                'puesto_id' => $data['puesto_id'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'] ?? null,
                'frecuencia_pago' => $data['frecuencia_pago'],
                'monto' => $data['monto'],
                'renovable' => $data['renovable'] ?? true,
            ]);

            // Generar pagos automáticamente según frecuencia
            $this->generarPagosParaContrato($contrato);

            return $contrato;
        });
    }

    public function show(Contrato $contrato)
    {
        $contrato->load('pagos','cliente','puesto.categoria');
        return view('contratos.show', compact('contrato'));
    }

    public function destroy(Contrato $contrato)
    {
        // Esto borrará pagos por cascada (migración)
        $contrato->delete();
        return redirect()->route('contratos.index')->with('success', 'Contrato eliminado.');
    }

    /**
     * Genera los registros de pagos (cartilla) para un contrato.
     * - Si fecha_fin es null -> genera 12 meses / 52 semanas por defecto (configurable)
     * - Si frecuencia SEMANAL y la feria es JUEVES: programará los pagos en el día JUEVES correspondiente.
     */
    protected function generarPagosParaContrato(Contrato $contrato)
    {
        $start = Carbon::parse($contrato->fecha_inicio);
        $end = $contrato->fecha_fin ? Carbon::parse($contrato->fecha_fin) : null;

        // Si no hay fecha_fin generamos por defecto un año (configurable).
        if (!$end) {
            // 1 año por defecto
            $end = (clone $start)->addYear();
        }

        $frecuencia = $contrato->frecuencia_pago;
        $monto = $contrato->monto;

        $dates = $this->calcularFechasDePago($start, $end, $frecuencia);

        foreach ($dates as $d) {
            Pago::create([
                'contrato_id' => $contrato->id,
                'accesor_id' => $contrato->puesto->categoria->accesor_id ?? null,
                'fecha_a_pagar' => $d->toDateString(),
                'monto' => $monto,
                'estado' => 'PENDIENTE',
            ]);
        }
    }

    /**
     * Calcula las fechas de pago entre start y end según frecuencia.
     * - SEMANAL: una vez por semana (ajustado al JUEVES si se requiere)
     * - MENSUAL: misma fecha cada mes (si no existe, ajusta al último día del mes)
     * - ANUAL: cada año
     *
     * Retorna array de Carbon dates.
     */
    protected function calcularFechasDePago(\Carbon\Carbon $start, \Carbon\Carbon $end, string $frecuencia)
    {
        $dates = [];
        $current = $start->copy();

        if ($frecuencia === 'SEMANAL') {
            // Ajustar a jueves más cercano >= fecha_inicio (opcional: feria solo jueves)
            $current = $current->next(\Carbon\Carbon::THURSDAY)->isPast()
                ? $current->copy()->next(\Carbon\Carbon::THURSDAY)
                : $current->copy()->nextOrSame(\Carbon\Carbon::THURSDAY);

            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addWeek();
            }
        } elseif ($frecuencia === 'MENSUAL') {
            // Mantener día del mes del start
            $day = $start->day;
            $current = $start->copy();
            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addMonthNoOverflow();
            }
        } else { // ANUAL
            $current = $start->copy();
            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addYear();
            }
        }

        return $dates;
    }
}