<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\CategoriaEstablecimiento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\Cartilla;
use App\Models\Accesor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PuestoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $accesor = $user->accesor;
        $accesors = Accesor::select('id', 'nombres', 'dni')->orderBy('nombres')->get();

        /**
         * CASO 1: Si el usuario autenticado es un ACCESOR
         */
        if ($accesor) {
            // Categorías con puestos del accesor
            $categorias = CategoriaEstablecimiento::whereHas('puestos.accesores', function ($q) use ($accesor) {
                $q->where('accesor_id', $accesor->id);
            })
            ->withCount(['puestos as puestos_count' => function ($q) use ($accesor) {
                $q->whereHas('accesores', function ($q2) use ($accesor) {
                    $q2->where('accesor_id', $accesor->id);
                });
            }])
            ->orderBy('nombre')
            ->get();

            // Si no hay categoría seleccionada → solo mostrar las categorías
            if (!$request->filled('categoria_id')) {
                return view('puestos.index', compact('categorias', 'accesors'));
            }

            // Si hay categoría seleccionada → mostrar puestos asignados al accesor
            $categoria = CategoriaEstablecimiento::findOrFail($request->categoria_id);

            $puestos = Puesto::with(['categoria', 'cliente', 'cliente.puestos.cartillas'])
                ->where('categoria_id', $categoria->id)
                ->whereHas('accesores', function ($q) use ($accesor) {
                    $q->where('accesor_id', $accesor->id);
                })
                ->orderBy('numero_puesto')
                ->paginate(15);

            // Cargar cartillas de los clientes relacionados a los puestos
            $puestosCartillas = Cartilla::whereIn('cliente_id', $puestos->pluck('cliente_id')->filter())->get();

            return view('puestos.index', compact('puestos', 'categoria', 'categorias', 'accesors', 'puestosCartillas'));
        }

        /**
         * CASO 2: Si el usuario es ADMIN o USER normal
         */
        $categorias = CategoriaEstablecimiento::withCount('puestos')->orderBy('nombre')->get();

        // Si no hay categoría seleccionada
        if (!$request->filled('categoria_id')) {
            return view('puestos.index', compact('categorias', 'accesors'));
        }

        // Si hay categoría seleccionada → mostrar puestos con clientes y cartillas
        $categoria = CategoriaEstablecimiento::findOrFail($request->categoria_id);

        $query = Puesto::with(['categoria', 'cliente', 'cliente.puestos.cartillas'])
            ->where('categoria_id', $categoria->id);

        // Filtros
        if ($request->filled('disponible')) {
            $query->where('disponible', (bool) $request->disponible);
        }

        if ($request->filled('numero_puesto')) {
            $query->where('numero_puesto', 'like', "%{$request->numero_puesto}%");
        }

        $puestos = $query->orderBy('numero_puesto')->paginate(15);

        $puestosCartillas = Cartilla::whereIn('cliente_id', $puestos->pluck('cliente_id')->filter())->get();

        return view('puestos.index', compact('puestos', 'categoria', 'categorias', 'accesors', 'puestosCartillas'));
    }

    public function create()
    {
        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombres')->get();
        return view('puestos.create', compact('categorias', 'clientes'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->accesor) {
            abort(403, 'No tienes permiso para crear puestos.');
        }
        $data = $request->validate([
            'categoria_id' => 'required|exists:categoria_establecimientos,id',
            'numero_puesto' => 'required|string|max:255|unique:puestos,numero_puesto',
            'cliente_id' => 'nullable|exists:clientes,id',
            'imagen_puesto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'servicios' => 'nullable|array',
            'servicios.*' => 'in:Agua,Luz,Otros',
            'observaciones' => 'nullable|string|max:1000',

            // Nuevos campos
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
            'primer_pago_fecha' => 'nullable|date',
            'accesor_cobro' => 'nullable|string|max:255',

            // Cliente temporal (enviado desde JS)
            'cliente_temp' => 'nullable|array',
            'cliente_temp.nombres' => 'nullable|string|max:255',
            'cliente_temp.dni' => 'nullable|string|max:20',
            'cliente_temp.celular' => 'nullable|string|max:20',
        ]);

        // Guardar imagen si se sube en public/images/puestos
        if ($request->hasFile('imagen_puesto')) {
            $imagen = $request->file('imagen_puesto');
            $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
            $rutaDestino = public_path('images/puestos');

            // Crea la carpeta si no existe
            if (!file_exists($rutaDestino)) {
                mkdir($rutaDestino, 0777, true);
            }

            // Mueve el archivo físicamente al directorio /public/images/puestos
            $imagen->move($rutaDestino, $nombreArchivo);

            // Guarda solo la ruta relativa (para usar en vistas con asset())
            $data['imagen_puesto'] = 'images/puestos/' . $nombreArchivo;
        }

        DB::transaction(function () use (&$data, $request) {
            // --- Manejar cliente temporal ---
            if ($request->filled('cliente_temp.dni')) {
                $temp = $request->cliente_temp;

                // Busca por DNI o crea si no existe
                $cliente = Cliente::firstOrCreate(
                    ['dni' => $temp['dni']],
                    [
                        'nombres' => $temp['nombres'] ?? 'N/D',
                        'celular' => $temp['celular'] ?? null,
                    ]
                );

                $data['cliente_id'] = $cliente->id;
            }

            // --- Crear el puesto ---
            $puesto = Puesto::create($data);

            // Vincular los accesores seleccionados
            if ($request->filled('accesores')) {
                $puesto->accesores()->sync($request->accesores);
            }

            // --- Vincular cliente (si hay) ---
            if (!empty($data['cliente_id'])) {
                $puesto->update(['disponible' => false]);

                // Crear automáticamente cartillas de los jueves usando el pago de la categoría
                if ($puesto->fecha_inicio && $puesto->fecha_fin) {
                    $fechaInicio = Carbon::parse($puesto->fecha_inicio);
                    $fechaFin = Carbon::parse($puesto->fecha_fin);

                    // Obtener monto desde la categoría asociada
                    $categoria = \App\Models\CategoriaEstablecimiento::find($puesto->categoria_id);
                    $montoPago = $categoria ? $categoria->pago_puesto : 0;

                    $fechaActual = $fechaInicio->copy();
                    $nro = 1;

                    while ($fechaActual->lte($fechaFin)) {
                        if ($fechaActual->isThursday()) {
                            Cartilla::create([
                                'puesto_id' => $puesto->id,
                                'cliente_id' => $puesto->cliente_id,
                                'nro' => $nro++,
                                'fecha_pagar' => $fechaActual->format('Y-m-d'),
                                'cuota' => $montoPago,
                                'observacion' => 'Pendiente',
                                'modo_pago' => 'SEMANAL',
                                'accesor_cobro' => $puesto->accesor_cobro,
                            ]);
                        }

                        $fechaActual->addDay(); 
                    }
                }

            }
        });

        return redirect()->route('puestos.index')->with('success', 'Puesto registrado correctamente.');
    }

    public function edit(Puesto $puesto)
    {
        if (auth()->user()->accesor) {
            abort(403, 'No tienes permiso para eliminar puestos.');
        }
        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombres')->get();
        return view('puestos.edit', compact('puesto', 'categorias', 'clientes'));
    }

    public function update(Request $request, Puesto $puesto)
    {
        if (auth()->user()->accesor) {
            abort(403, 'No tienes permiso para eliminar puestos.');
        }
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
            'accesor_cobro' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('imagen_puesto')) {
            $imagen = $request->file('imagen_puesto');
            $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
            $rutaDestino = public_path('images/puestos');

            // Crea la carpeta si no existe
            if (!file_exists($rutaDestino)) {
                mkdir($rutaDestino, 0777, true);
            }

            // Mueve el archivo físicamente
            $imagen->move($rutaDestino, $nombreArchivo);

            // Guarda solo la ruta relativa (para mostrar luego en las vistas)
            $data['imagen_puesto'] = 'images/puestos/' . $nombreArchivo;
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
        if (auth()->user()->accesor) {
            abort(403, 'No tienes permiso para eliminar puestos.');
        }
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
            if ($fecha->isThursday()) { 
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

        // Recorremos todas las fechas dentro del rango
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

            // Avanzamos según el modo de pago
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
