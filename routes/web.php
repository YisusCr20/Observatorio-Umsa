<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\FeedbackController;

use App\Http\Controllers\Admin\WelcomeContentController;

use App\Models\Horario;
use App\Models\GalleryImage;
use App\Models\PageSection;
use App\Models\PublicContentItem;
use App\Models\WelcomeSlide;
use App\Models\WelcomeSetting;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
| Accesibles para cualquier visitante sin iniciar sesión.
*/

Route::get('/', function () {
    $settings = WelcomeSetting::pluck('value', 'key');

    $slides = WelcomeSlide::where('is_active', true)
        ->orderBy('position')
        ->get();

    return view('bienvenido', compact('settings', 'slides'));
})->name('bienvenido');

Route::get('/acerca', function () {
    $aboutSections = Schema::hasTable('page_sections')
        ? PageSection::where('page', 'acerca')->where('is_active', true)->orderBy('position')->get()->keyBy('section_key')
        : collect();

    return view('acerca', compact('aboutSections'));
})->name('acerca');

Route::get('/investigacion', function () {
    $researchItems = Schema::hasTable('public_content_items')
        ? PublicContentItem::where('page', 'investigacion')->where('is_active', true)->orderBy('position')->get()
        : collect();

    return view('investigacion', compact('researchItems'));
})->name('investigacion');

Route::get('/eventos', function () {
    $eventItems = Schema::hasTable('public_content_items')
        ? PublicContentItem::where('page', 'eventos')->where('is_active', true)->orderBy('position')->get()
        : collect();

    return view('eventos', compact('eventItems'));
})->name('eventos');

/*
|--------------------------------------------------------------------------
| GALERÍA
|--------------------------------------------------------------------------
| Por ahora apunta a contactos.blade.php porque en tu proyecto todavía existe
| resources/views/contactos.blade.php.
|
| Si luego creas resources/views/galeria.blade.php, cambia 'contactos' por 'galeria'.
*/

Route::get('/galeria', function () {
    $galleryImages = Schema::hasTable('gallery_images')
        ? GalleryImage::where('is_active', true)->orderBy('position')->get()
        : collect();

    return view('galeria', compact('galleryImages'));
})->name('galeria');

Route::view('/contacto', 'contactos')->name('contacto');

Route::match(['get', 'post'], '/chatbot/reservas', [ChatbotController::class, 'reservas'])
    ->name('chatbot.reservas');


/*
|--------------------------------------------------------------------------
| 3. RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'no-cache'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | NOTIFICACIONES
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications/clear-unread', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Notificaciones marcadas como leídas.');
    })->name('notifications.markRead');


    /*
    |--------------------------------------------------------------------------
    | REDIRECCIÓN INTELIGENTE POR ROL
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        session()->reflash();

        $role = strtolower(Auth::user()->role ?? 'usuario');
        $query = $request->only(['panel', 'fecha_inicio', 'fecha_fin', 'preset']);

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard', $query),
            'secretaria' => redirect()->route('secretaria.dashboard', $query),
            default => redirect()->route('user.dashboard', $query),
        };
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | DASHBOARDS POR ROL
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/secretaria/dashboard', [SecretariaController::class, 'index'])
        ->middleware('role:secretaria')
        ->name('secretaria.dashboard');

    Route::get('/usuario/dashboard', [ReservaController::class, 'dashboard'])
        ->name('user.dashboard');

    Route::post('/usuario/feedback', [FeedbackController::class, 'store'])
        ->name('feedback.store');


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN DE CONTENIDO PÚBLICO
    |--------------------------------------------------------------------------
    | Aquí el administrador podrá editar imágenes, fondos y slides.
    */

   Route::middleware(['role:admin'])
    ->prefix('admin/contenido')
    ->name('admin.contenido.')
    ->group(function () {

        Route::get('/bienvenido', [WelcomeContentController::class, 'edit'])
            ->name('bienvenido.edit');

        Route::post('/bienvenido/fondos', [WelcomeContentController::class, 'updateBackgrounds'])
            ->name('bienvenido.fondos');

        Route::post('/bienvenido/slides', [WelcomeContentController::class, 'storeSlide'])
            ->name('bienvenido.slides.store');

        Route::put('/bienvenido/slides/{slide}', [WelcomeContentController::class, 'updateSlide'])
            ->name('bienvenido.slides.update');

        Route::delete('/bienvenido/slides/{slide}', [WelcomeContentController::class, 'destroySlide'])
            ->name('bienvenido.slides.destroy');

        Route::post('/acerca', [WelcomeContentController::class, 'updateAbout'])
            ->name('acerca.update');

        Route::post('/galeria/imagenes', [WelcomeContentController::class, 'storeGalleryImage'])
            ->name('galeria.images.store');

        Route::delete('/galeria/imagenes/{image}', [WelcomeContentController::class, 'destroyGalleryImage'])
            ->name('galeria.images.destroy');

        Route::post('/eventos/items', [WelcomeContentController::class, 'storePublicItem'])
            ->defaults('page', 'eventos')
            ->name('eventos.items.store');

        Route::put('/items/{item}', [WelcomeContentController::class, 'updatePublicItem'])
            ->name('items.update');

        Route::delete('/items/{item}', [WelcomeContentController::class, 'destroyPublicItem'])
            ->name('items.destroy');

        Route::post('/investigacion/items', [WelcomeContentController::class, 'storePublicItem'])
            ->defaults('page', 'investigacion')
            ->name('investigacion.items.store');

        Route::view('/acerca', 'admin.contenido.acerca.edit')->name('acerca.edit');
        Route::view('/eventos', 'admin.contenido.eventos.edit')->name('eventos.edit');
        Route::view('/galeria', 'admin.contenido.galeria.edit')->name('galeria.edit');
        Route::view('/investigacion', 'admin.contenido.investigacion.edit')->name('investigacion.edit');
    });
    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN GENERAL
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/registrar-usuario', [UserController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.users.store');

    Route::post('/admin/roles', [AdminController::class, 'storeRole'])
        ->middleware('role:admin')
        ->name('admin.roles.store');

    Route::patch('/admin/usuarios/{user}/lista-negra', [AdminController::class, 'blacklistUser'])
        ->middleware('role:admin')
        ->name('admin.users.blacklist');

    Route::patch('/admin/usuarios/{user}/restaurar', [AdminController::class, 'restoreUser'])
        ->middleware('role:admin')
        ->name('admin.users.restore');

    Route::delete('/admin/usuarios/{user}', [AdminController::class, 'destroyUser'])
        ->middleware('role:admin')
        ->name('admin.users.destroy');

    Route::post('/admin/guias/asignaciones', [AdminController::class, 'storeGuideAssignment'])
        ->middleware('role:admin')
        ->name('admin.guias.store');

    Route::delete('/admin/guias/asignaciones/{guideAssignment}', [AdminController::class, 'destroyGuideAssignment'])
        ->middleware('role:admin')
        ->name('admin.guias.destroy');

    Route::post('/admin/invitados-especiales', [AdminController::class, 'storeSpecialGuestReservation'])
        ->middleware('role:admin')
        ->name('admin.invitados.store');

    Route::delete('/admin/invitados-especiales/{specialGuestReservation}', [AdminController::class, 'destroySpecialGuestReservation'])
        ->middleware('role:admin')
        ->name('admin.invitados.destroy');

    Route::get('/admin/reportes', [AdminController::class, 'reportes'])
        ->middleware('role:admin')
        ->name('admin.reportes.index');

    Route::get('/admin/reportes/reservas-pdf', [AdminController::class, 'reservasPdf'])
        ->middleware('role:admin')
        ->name('admin.reportes.reservas.pdf');

    Route::get('/admin/reportes/usuarios-pdf', [AdminController::class, 'usuariosPdf'])
        ->middleware('role:admin')
        ->name('admin.reportes.usuarios.pdf');

    Route::get('/admin/backups/base-datos', [AdminController::class, 'downloadDatabaseBackup'])
        ->middleware('role:admin')
        ->name('admin.backups.database');

    Route::get('/admin/logs/descargar', [AdminController::class, 'downloadLog'])
        ->middleware('role:admin')
        ->name('admin.logs.download');

    Route::post('/admin/logs/limpiar', [AdminController::class, 'clearLog'])
        ->middleware('role:admin')
        ->name('admin.logs.clear');


    /*
    |--------------------------------------------------------------------------
    | MÓDULO EXCLUSIVO: SECRETARIA
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:secretaria'])
        ->prefix('secretaria')
        ->name('secretaria.')
        ->group(function () {

            Route::get('/dashboard', [SecretariaController::class, 'index'])
                ->name('dashboard');

            Route::get('/reservas-usuarios', [SecretariaController::class, 'reservasIndex'])
                ->name('reservas.index');

            /*
            | Rutas estáticas primero para evitar conflicto con /reservas/{reserva}
            */

            Route::get('/reservas/pendientes', [SecretariaController::class, 'indexPendientes'])
                ->name('reservas.pendientes');

            Route::get('/historial-reservas', [SecretariaController::class, 'historialReservas'])
                ->name('reservas.historial');

            Route::get('/pagos/verificar', [SecretariaController::class, 'historialPagos'])
                ->name('pagos.verificar');

            Route::post('/pagos/manual', [SecretariaController::class, 'registrarPagoManual'])
                ->name('pagos.manual.store');

            Route::get('/pagos/reporte-pdf', [SecretariaController::class, 'pagosPdf'])
                ->name('pagos.pdf');

            Route::get('/reportes', [SecretariaController::class, 'panelReportes'])
                ->name('reportes.index');

            Route::post('/reportes/exportar-pdf', [SecretariaController::class, 'exportarReportePdf'])
                ->name('reportes.pdf');

            /*
            | Rutas dinámicas de reservas
            */

            Route::get('/reservas/{reserva}', [ReservaController::class, 'show'])
                ->name('reservas.show');

            Route::get('/reservas/{reserva}/editar', [ReservaController::class, 'edit'])
                ->name('reservas.edit');

            Route::put('/reservas/{reserva}', [ReservaController::class, 'update'])
                ->name('reservas.update');

            Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])
                ->name('reservas.destroy');

            Route::patch('/reservas/{id}/status', [SecretariaController::class, 'updateStatus'])
                ->name('reservas.status');

            Route::post('/reservas/{reserva}/approve', [ReservaController::class, 'approve'])
                ->name('reservas.approve');

            Route::post('/reservas/{reserva}/reject', [ReservaController::class, 'reject'])
                ->name('reservas.reject');
        });


    /*
    |--------------------------------------------------------------------------
    | PERFIL DE USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get('/usuario/perfil', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | MÓDULO DE RESERVAS
    |--------------------------------------------------------------------------
    */

    Route::prefix('reservas')
        ->name('reservas.')
        ->group(function () {

            Route::get('/', [ReservaController::class, 'index'])
                ->name('index');

            Route::get('/crear', [ReservaController::class, 'create'])
                ->name('create');

            Route::get('/disponibilidad', [ReservaController::class, 'disponibilidad'])
                ->name('disponibilidad');

            /*
            | Ruta principal recomendada
            */

            Route::post('/guardar', [ReservaController::class, 'store'])
                ->name('store');

            /*
            | Ruta adicional para que también funcione fetch('/reservas')
            */

            Route::post('/', [ReservaController::class, 'store'])
                ->name('store.direct');

            Route::get('/{reserva}', [ReservaController::class, 'show'])
                ->name('show');

            Route::get('/{reserva}/editar', [ReservaController::class, 'edit'])
                ->name('edit');

            Route::put('/{reserva}', [ReservaController::class, 'update'])
                ->name('update');

            Route::delete('/{reserva}', [ReservaController::class, 'destroy'])
                ->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | API Y UTILIDADES ASÍNCRONAS
    |--------------------------------------------------------------------------
    */

    Route::get('/api/turnos/{turno}/horarios', function ($turnoId) {
        return Horario::where('turno_id', $turnoId)->get();
    });
});


require __DIR__ . '/auth.php';
