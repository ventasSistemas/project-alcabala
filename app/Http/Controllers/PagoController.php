<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Contrato;
use App\Models\Cartilla;
use App\Models\CategoriaEstablecimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // si usas barryvdh/laravel-dompdf
use Carbon\Carbon;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::orderBy('created_at', 'desc')->get();
        return view('pagos.index', compact('pagos'));
    }

    public function show($id)
    {
        $pago = Pago::findOrFail($id);
        return view('pagos.show', compact('pago'));
    }
}