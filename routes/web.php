<?php

use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\MarcacionController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PapeletaController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// breeze:install genera App\Http\Controllers\ProfileController y las vistas
// resources/views/profile/*, pero NO agrega estas rutas a auth.php — van en
// web.php. El layout de Breeze (navigation-menu) las referencia siempre.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // ---------- Papeletas: accesible a los 3 roles, filtrado por Policy/scope ----------
    Route::get('/papeletas', [PapeletaController::class, 'index'])->name('papeletas.index');

    // ---------- Exportación a CSV: solo RRHH ----------
    Route::middleware(['role:RRHH'])->group(function () {
        Route::get('/papeletas/exportar', [PapeletaController::class, 'exportar'])->name('papeletas.exportar');
    });

    Route::get('/papeletas/crear', [PapeletaController::class, 'create'])->name('papeletas.create');
    Route::post('/papeletas', [PapeletaController::class, 'store'])->name('papeletas.store');
    Route::get('/papeletas/{papeleta}', [PapeletaController::class, 'show'])->name('papeletas.show');

    // ---------- Flujo de aprobación: Jefe (SOLICITADO) y RRHH (APROBADO_JEFE) ----------
    Route::post('/papeletas/{papeleta}/aprobar', [AprobacionController::class, 'aprobar'])->name('papeletas.aprobar');
    Route::post('/papeletas/{papeleta}/rechazar', [AprobacionController::class, 'rechazar'])->name('papeletas.rechazar');
    Route::post('/papeletas/{papeleta}/observar', [AprobacionController::class, 'observar'])->name('papeletas.observar');

    // ---------- Marcación GPS: solo el propio trabajador ----------
    Route::post('/papeletas/{papeleta}/marcar-salida', [MarcacionController::class, 'salida'])->name('papeletas.marcar-salida');
    Route::post('/papeletas/{papeleta}/marcar-retorno', [MarcacionController::class, 'retorno'])->name('papeletas.marcar-retorno');

    // ---------- Adjuntos ----------
    Route::post('/papeletas/{papeleta}/adjuntos', [AdjuntoController::class, 'store'])->name('adjuntos.store');
    Route::get('/adjuntos/{adjunto}/descargar', [AdjuntoController::class, 'download'])->name('adjuntos.download');
    Route::delete('/adjuntos/{adjunto}', [AdjuntoController::class, 'destroy'])->name('adjuntos.destroy');

    // ---------- Notificaciones (campana) ----------
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{notificacion}/leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leida');
    Route::post('/notificaciones/leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leidas');

    // ---------- Suscripción Web Push ----------
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push.store');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push.destroy');

    // ---------- Panel y catálogos: solo ADMINISTRADOR ----------
    Route::middleware(['role:ADMINISTRADOR'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');

        Route::resource('areas', AreaController::class)->except(['show']);
        Route::resource('cargos', CargoController::class)->except(['show']);
        Route::resource('sedes', SedeController::class)->except(['show']);
        Route::resource('motivos', MotivoController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
});
