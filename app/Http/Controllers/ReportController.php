<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Estados disponibles
        $estados = ['PAGADO', 'PAGO ATRASADO'];

        // Base de consulta
        $query = Pago::query()->orderBy('fecha_a_pagar', 'desc');

        // Filtro por fecha de inicio
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_a_pagar', '>=', $request->fecha_inicio);
        }

        // Filtro por fecha fin
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_a_pagar', '<=', $request->fecha_fin);
        }

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'TODOS') {
            $query->where('estado', $request->estado);
        }

        // Resultados
        $pagos = $query->paginate(15)->appends($request->query());

        return view('reports.index', compact('pagos', 'estados'));
    }
}
