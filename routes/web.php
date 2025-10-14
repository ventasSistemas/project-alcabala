<?php

use Illuminate\Support\Facades\Route;

// Importar controladores
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccesorController;
use App\Http\Controllers\CategoriaEstablecimientoController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CartillaController;
use App\Http\Controllers\ReportController;



// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware(['auth', 'accesor'])->group(function () {
    
    Route::resource('puestos', PuestoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('pagos', PagoController::class);
});

Route::middleware('auth')->group(function () {


    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::resource('accesores', AccesorController::class);
    Route::resource('categorias', CategoriaEstablecimientoController::class);
    Route::get('/categorias/{id}/info', [App\Http\Controllers\CategoriaEstablecimientoController::class, 'getInfo'])->name('categorias.info');

    Route::resource('puestos', PuestoController::class);
    Route::post('puestos/asignar', [PuestoController::class, 'asignarMultiples'])->name('puestos.asignar');

    Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::resource('clientes', ClienteController::class);
    Route::resource('contratos', ContratoController::class);
    Route::get('contratos/{contrato}/cartilla', [PagoController::class, 'showByContrato'])->name('contratos.cartilla');
    Route::get('/puestos/{id}/cartilla', [PuestoController::class, 'verCartilla'])->name('puestos.cartilla');

    Route::resource('pagos', PagoController::class);
    Route::post('pagos/{pago}/marcar', [PagoController::class, 'marcarPagado'])->name('pagos.marcar');
    //Route::view('reports', 'reports.index')->name('reports.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    Route::get('/clientes/{id}/cartillas', [CartillaController::class, 'cartillasCliente']);
    Route::get('/cartillas', [CartillaController::class, 'listaGeneral'])->name('cartillas.lista');

    Route::prefix('cartillas')->group(function () {
        Route::get('/{puesto}', [CartillaController::class, 'index'])->name('cartillas.index');
        Route::post('/generar/{puesto}', [CartillaController::class, 'generar'])->name('cartillas.generar');
        Route::patch('/estado/{cartilla}', [CartillaController::class, 'actualizarEstado'])->name('cartillas.estado');
        Route::get('/imprimir/{puesto}', [CartillaController::class, 'imprimir'])->name('cartillas.imprimir');

        
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/cartillas/{cartilla}/estado/{estado}', [App\Http\Controllers\CartillaController::class, 'cambiarEstado'])
        ->name('cartillas.cambiarEstado');

        Route::get('/ticket/{cartillas}', [CartillaController::class, 'generarTicket'])
            ->name('cartillas.ticket');

        Route::post('/ingresar-pagos', [CartillaController::class, 'ingresarPagos'])
        ->name('cartillas.ingresarPagos');


    });

    Route::get('/notificaciones/{id}/leer', function ($id) {
        $notificacion = auth()->user()->notifications()->find($id);

        if ($notificacion) {
            $notificacion->markAsRead(); // ✅ Marcar como leída

            // Redirigir al detalle del pago (no a la lista completa)
            return redirect()->route('pagos.show', $notificacion->data['pago_id']);
        }

        return redirect()->route('pagos.index');
    })->name('notificaciones.leer');

});
