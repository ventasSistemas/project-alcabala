<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\CategoriaEstablecimiento;
use App\Models\Accesor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PagosExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['contrato.puesto.categoria','contrato.cliente','accesor']);

        // Aplicar filtros opcionales
        if ($request->filled('categoria_id')) {
            $query->whereHas('contrato.puesto', fn($q) => $q->where('categoria_id', $request->categoria_id));
        }

        if ($request->filled('dni')) {
            $query->whereHas('contrato.cliente', fn($q) => $q->where('dni', $request->dni));
        }

        if ($request->filled('numero_puesto')) {
            $query->whereHas('contrato.puesto', fn($q) => $q->where('numero_puesto', 'like', "%{$request->numero_puesto}%"));
        }

        if ($request->filled('accesor_id')) {
            $query->where('accesor_id', $request->accesor_id);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_a_pagar', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_a_pagar', '<=', $request->fecha_fin);
        }

        if ($request->filled('estado') && $request->estado !== 'TODOS') {
            $query->where('estado', $request->estado);
        }

        $pagos = $query->orderBy('fecha_a_pagar')->paginate(25);

        $categorias = CategoriaEstablecimiento::orderBy('nombre')->get();
        $accesores = Accesor::orderBy('nombres_completos')->get();

        return view('reports.index', compact('pagos','categorias','accesores'));
    }

    public function export(Request $request)
    {
        $query = Pago::with(['contrato.puesto.categoria','contrato.cliente','accesor']);

        // aplicar mismos filtros que en index
        if ($request->filled('categoria_id')) {
            $query->whereHas('contrato.puesto', fn($q) => $q->where('categoria_id', $request->categoria_id));
        }
        if ($request->filled('dni')) {
            $query->whereHas('contrato.cliente', fn($q) => $q->where('dni', $request->dni));
        }
        if ($request->filled('numero_puesto')) {
            $query->whereHas('contrato.puesto', fn($q) => $q->where('numero_puesto', 'like', "%{$request->numero_puesto}%"));
        }
        if ($request->filled('accesor_id')) {
            $query->where('accesor_id', $request->accesor_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_a_pagar', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_a_pagar', '<=', $request->fecha_fin);
        }
        if ($request->filled('estado') && $request->estado !== 'TODOS') {
            $query->where('estado', $request->estado);
        }

        $pagos = $query->orderBy('fecha_a_pagar')->get();

        $format = $request->get('format', 'pdf');

        if ($format === 'excel') {
            return Excel::download(new PagosExport($pagos), 'pagos.xlsx');
        }

        // PDF
        $pdf = Pdf::loadView('reports.pdf', compact('pagos'));
        return $pdf->download('pagos.pdf');
    }
}