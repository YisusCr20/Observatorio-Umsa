<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Models\Horario;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
| Accesibles para cualquier visitante sin iniciar sesión.
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('bienvenido');
})->name('bienvenido');

Route::view('/acerca', 'acerca')->name('acerca');
Route::view('/investigacion', 'investigacion')->name('investigacion');
Route::view('/eventos', 'eventos')->name('eventos');
Route::view('/contacto', 'contacto')->name('contacto');


/*
|--------------------------------------------------------------------------
| 2. RECUPERACIÓN DE CONTRASEÑAS (FUERA DEL AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});


/*
|--------------------------------------------------------------------------
| 3. RUTAS PROTEGIDAS (AUTH & NO-CACHE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'no-cache'])->group(function () {

    /* 🔔 GLOBAL: MARCAR NOTIFICACIONES COMO LEÍDAS (Campanita de la Secretaría) */
    Route::get('/notifications/clear-unread', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Notificaciones marcadas como leídas.');
    })->name('notifications.markRead');

    /* --- REDIRECCIÓN INTELIGENTE --- */
    Route::get('/dashboard', function () {
        session()->reflash();
        $role = strtolower(Auth::user()->role ?? 'usuario');

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'secretaria' => redirect()->route('secretaria.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    })->name('dashboard');

    /* --- DASHBOARDS POR ROL --- */
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    /* 🌟 CORRECCIÓN AQUÍ: Apuntaba a un método genérico, lo vinculamos al flujo de la app */
    Route::get('/usuario/dashboard', [ReservaController::class, 'dashboard'])
        ->name('user.dashboard');


    /*
    |--------------------------------------------------------------------------
    | MÓDULO EXCLUSIVO: SECRETARIA
    |--------------------------------------------------------------------------
    | Todas las rutas aquí dentro llevarán automáticamente:
    | - Prefijo en la URL: /secretaria/...
    | - Prefijo en el Nombre: secretaria....
    */
    Route::middleware(['role:secretaria'])->prefix('secretaria')->name('secretaria.')->group(function () {

        // 1. Vista Principal (Dashboard de control estadístico)
        Route::get('/dashboard', [SecretariaController::class, 'index'])->name('dashboard');

        // 2. Listado Maestro de Reservas de Usuarios (La raíz de la tabla)
        // URL Real: /secretaria/reservas-usuarios | Nombre completo Laravel: secretaria.reservas.index
        Route::get('/reservas-usuarios', [SecretariaController::class, 'reservasIndex'])->name('reservas.index');

        // 3. Operaciones individuales de Gestión para la Secretaria
        // Esto evita que al presionar ver, editar o eliminar se salga de su entorno administrativo.
        Route::get('/reservas/{reserva}', [ReservaController::class, 'show'])->name('reservas.show');
        Route::get('/reservas/{reserva}/editar', [ReservaController::class, 'edit'])->name('reservas.edit');
        Route::put('/reservas/{reserva}', [ReservaController::class, 'update'])->name('reservas.update');
        Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

        // 4. Control de Reservas y Cambios de Estado Financiero
        Route::get('/reservas/pendientes', [SecretariaController::class, 'indexPendientes'])->name('reservas.pendientes');
        Route::patch('/reservas/{id}/status', [SecretariaController::class, 'updateStatus'])->name('reservas.status');

        // 5. Flujo rápido de aprobación/rechazo tras revisar el comprobante de pago
        Route::post('/reservas/{reserva}/approve', [ReservaController::class, 'approve'])->name('reservas.approve');
        Route::post('/reservas/{reserva}/reject', [ReservaController::class, 'reject'])->name('reservas.reject');

        // 6. Auditoría de Pagos e Historial de Registros Manuales
        Route::get('/pagos/verificar', [SecretariaController::class, 'historialPagos'])->name('pagos.verificar');
        Route::post('/pagos/manual', [SecretariaController::class, 'registrarPagoManual'])->name('pagos.manual.store');

        // 7. Historial Cronológico de todas las Reservas registradas en el sistema
        Route::get('/historial-reservas', [SecretariaController::class, 'historialReservas'])->name('reservas.historial');

        // 8. Reportes institucionales en formato PDF
        // Si la secretaria accede por URL a /secretaria/reportes, el controlador redirige a la tabla interactiva
        Route::get('/reportes', [SecretariaController::class, 'panelReportes'])->name('reportes.index');

        // 🌟 CORRECCIÓN CRÍTICA: Se removió la redundancia '/secretaria' en la URL física y se ajustó el alias 
        // URL Real limpia: /secretaria/reportes/exportar-pdf | Nombre Laravel: secretaria.reportes.pdf
        Route::post('/reportes/exportar-pdf', [SecretariaController::class, 'exportarReportePdf'])->name('reportes.pdf');
    });


    /* --- GESTIÓN DE PERFIL --- */
    Route::get('/usuario/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* --- ADMINISTRACIÓN GENERAL (ADMIN) --- */
    Route::post('/admin/registrar-usuario', [UserController::class, 'store'])->name('admin.users.store');

    /* --- MÓDULO DE RESERVAS (CRUD COMPLETO PARA USUARIOS) --- */
    Route::prefix('reservas')->name('reservas.')->group(function () {
        Route::get('/', [ReservaController::class, 'index'])->name('index');
        Route::get('/crear', [ReservaController::class, 'create'])->name('create');
        Route::post('/guardar', [ReservaController::class, 'store'])->name('store');
        Route::get('/{reserva}', [ReservaController::class, 'show'])->name('show');
        Route::get('/{reserva}/editar', [ReservaController::class, 'edit'])->name('edit');
        Route::put('/{reserva}', [ReservaController::class, 'update'])->name('update');
        Route::delete('/{reserva}', [ReservaController::class, 'destroy'])->name('destroy');
    });

    /* --- API Y UTILIDADES ASÍNCRONAS --- */
    Route::get('/api/turnos/{turno}/horarios', function ($turnoId) {
        return Horario::where('turno_id', $turnoId)->get();
    });
});

require __DIR__ . '/auth.php';