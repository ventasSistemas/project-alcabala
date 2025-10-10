<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\CategoriaEstablecimiento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Puesto::with(['categoria', 'cliente']);

        // Filtros opcionales
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('disponible')) {
            $query->where('disponible', (bool) $request->disponible);
        }
        if ($request->filled('cliente_dni')) {
            $query->whereHas('cliente', fn($q) => $q->where('dni', $request->cliente_dni));
        }
        if ($request->filled('numero_puesto')) {
            $query->where('numero_puesto', 'like', "%{$request->numero_puesto}%");
        }

        $puestos = $query->orderBy('numero_puesto')->paginate(15);
        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();

        return view('puestos.index', compact('puestos', 'categorias'));
    }

    public function create()
    {
        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombres')->get();
        return view('puestos.create', compact('categorias', 'clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => 'required|exists:categoria_establecimientos,id',
            'numero_puesto' => 'required|string|max:255|unique:puestos,numero_puesto',
            'cliente_id' => 'nullable|exists:clientes,id',
            'imagen_puesto' => 'nullable|image|max:2048',
            'servicios' => 'nullable|array',
            'servicios.*' => 'in:Agua,Luz,Otros',
            'observaciones' => 'nullable|string|max:1000',

            // Nuevos campos
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_apertura' => 'required|date_format:H:i',
            'hora_cierre' => 'required|date_format:H:i',
            'primer_pago_fecha' => 'nullable|date',
            'primer_pago_monto' => 'nullable|numeric|min:0',
            'modo_pago' => 'nullable|in:SEMANAL,MENSUAL,ANUAL',
            'accesor_cobro' => 'nullable|string|max:255',
        ]);

        // Manejar imagen si se sube
        if ($request->hasFile('imagen_puesto')) {
            $data['imagen_puesto'] = $request->file('imagen_puesto')->store('puestos', 'public');
        }

        Puesto::create($data);

        return redirect()->route('puestos.index')->with('success', 'Puesto registrado correctamente.');
    }

    public function edit(Puesto $puesto)
    {
        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombres')->get();
        return view('puestos.edit', compact('puesto', 'categorias', 'clientes'));
    }

    public function update(Request $request, Puesto $puesto)
    {
        $data = $request->validate([
            'categoria_id' => 'required|exists:categoria_establecimientos,id',
            'numero_puesto' => 'required|string|max:255|unique:puestos,numero_puesto,' . $puesto->id,
            'cliente_id' => 'nullable|exists:clientes,id',
            'imagen_puesto' => 'nullable|image|max:2048',
            'servicios' => 'nullable|array',
            'servicios.*' => 'in:Agua,Luz,Otros',
            'observaciones' => 'nullable|string|max:1000',

            // Nuevos campos
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
            'primer_pago_fecha' => 'nullable|date',
            'primer_pago_monto' => 'nullable|numeric|min:0',
            'modo_pago' => 'nullable|in:SEMANAL,MENSUAL,ANUAL',
            'accesor_cobro' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('imagen_puesto')) {
            $data['imagen_puesto'] = $request->file('imagen_puesto')->store('puestos', 'public');
        }

        $puesto->update($data);

        return redirect()->route('puestos.index')->with('success', 'Puesto actualizado correctamente.');
    }

    public function show(Puesto $puesto)
    {
        $puesto->load(['cliente', 'categoria', 'contratos' => function ($q) {
            $q->latest()->take(5);
        }]);

        return view('puestos.show', compact('puesto'));
    }

    public function destroy(Puesto $puesto)
    {
        $puesto->delete();
        return redirect()->route('puestos.index')->with('success', 'Puesto eliminado correctamente.');
    }

    /**
     * Asignar varios puestos a un cliente
     */
    public function asignarMultiples(Request $request)
    {
        $data = $request->validate([
            'puesto_ids' => 'required|array|min:1',
            'puesto_ids.*' => 'exists:puestos,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_dni' => 'nullable|string|size:8',
            'cliente_nombres' => 'nullable|string|max:255',
            'cliente_apellidos' => 'nullable|string|max:255',
            'cliente_celular' => 'nullable|string|max:20',
            'frecuencia_pago' => 'required|in:SEMANAL,MENSUAL,ANUAL',
            'fecha_inicio' => 'required|date',
            'monto' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $cliente = $data['cliente_id']
                ? Cliente::find($data['cliente_id'])
                : Cliente::firstOrCreate(
                    ['dni' => $data['cliente_dni']],
                    [
                        'nombres' => $data['cliente_nombres'] ?? 'N/D',
                        'apellidos' => $data['cliente_apellidos'] ?? '',
                        'celular' => $data['cliente_celular'] ?? null,
                    ]
                );

            foreach ($data['puesto_ids'] as $id) {
                $puesto = Puesto::findOrFail($id);
                $puesto->update(['cliente_id' => $cliente->id, 'disponible' => false]);

                app(\App\Http\Controllers\ContratoController::class)
                    ->storeFromAssignment([
                        'cliente_id' => $cliente->id,
                        'puesto_id' => $puesto->id,
                        'fecha_inicio' => $data['fecha_inicio'],
                        'frecuencia_pago' => $data['frecuencia_pago'],
                        'monto' => $data['monto'],
                        'renovable' => true,
                    ]);
            }
        });

        return redirect()->route('puestos.index')->with('success', 'Puestos asignados correctamente.');
    }

    public function generarCartillaPagos(Puesto $puesto)
    {
        $pagos = [];
        $inicio = $puesto->fecha_inicio;
        $fin = $puesto->fecha_fin;
        $monto = $puesto->primer_pago_monto ?? 0;
        $modo = $puesto->modo_pago;

        $periodo = match($modo) {
            'SEMANAL' => '1 week',
            'MENSUAL' => '1 month',
            'ANUAL' => '1 year',
            default => '1 month',
        };

        $fecha = $inicio->copy();
        $nro = 1;
        while ($fecha <= $fin) {
            if ($fecha->isThursday()) { // feria solo los jueves
                $pagos[] = [
                    'nro' => $nro++,
                    'fecha_pagar' => $fecha->format('d/m/Y'),
                    'cuota' => 'S/' . number_format($monto, 2),
                    'observacion' => 'Pendiente',
                ];
            }
            $fecha->add($periodo);
        }

        return view('puestos.cartilla', compact('puesto', 'pagos'));
    }

    public function verCartilla(Request $request, $id)
    {
        $puesto = Puesto::findOrFail($id);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $monto = floatval($request->primer_pago_monto ?? 0);
        $modoPago = $request->modo_pago ?? 'SEMANAL';

        $pagos = [];
        $nro = 1;
        $fechaActual = $fechaInicio->copy();

        // 🔹 Recorremos todas las fechas dentro del rango
        while ($fechaActual->lte($fechaFin)) {
            // Solo consideramos los jueves
            if ($fechaActual->isThursday()) {
                $pagos[] = [
                    'nro' => $nro++,
                    'fecha_pagar' => $fechaActual->format('Y-m-d'),
                    'cuota' => 'S/ ' . number_format($monto, 2),
                    'observacion' => 'Pendiente',
                ];
            }

            // 🔹 Avanzamos según el modo de pago
            if ($modoPago === 'SEMANAL') {
                $fechaActual->addWeek();
            } elseif ($modoPago === 'MENSUAL') {
                $fechaActual->addMonth();
            } elseif ($modoPago === 'ANUAL') {
                $fechaActual->addYear();
            }
        }

        return view('puestos.cartilla', compact('puesto', 'pagos'));
    }
}
