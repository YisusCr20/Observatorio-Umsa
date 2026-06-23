<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Role;
use App\Models\User;
use App\Models\WelcomeSetting;
use App\Models\WelcomeSlide;
use App\Models\GalleryImage;
use App\Models\GuideAssignment;
use App\Models\PageSection;
use App\Models\Pago;
use App\Models\PublicContentItem;
use App\Models\SpecialGuestReservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | USUARIOS INTERNOS
        |--------------------------------------------------------------------------
        */

        $blacklistColumnsReady = Schema::hasColumn('users', 'is_blacklisted');

        $totalUsuarios = User::count();
        $usuariosRegistrados = User::query()
            ->where('role', 'usuario')
            ->when($blacklistColumnsReady, fn ($query) => $query->where('is_blacklisted', false))
            ->count();

        $usuariosInternos = User::query()
            ->whereIn('role', ['admin', 'secretaria'])
            ->when($blacklistColumnsReady, fn ($query) => $query->where('is_blacklisted', false))
            ->latest()
            ->get();

        $usuariosGestion = User::query()
            ->when($blacklistColumnsReady, fn ($query) => $query->where('is_blacklisted', false))
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $usuariosListaNegra = $blacklistColumnsReady
            ? User::where('is_blacklisted', true)->latest('blacklisted_at')->get()
            : collect();

        if (Schema::hasTable('roles')) {
            foreach (['admin', 'secretaria', 'usuario'] as $baseRole) {
                Role::firstOrCreate(['nombre' => $baseRole]);
            }
        }

        $roles = Schema::hasTable('roles')
            ? Role::orderBy('nombre')->get()
            : collect();

        $guideAssignments = Schema::hasTable('guide_assignments')
            ? GuideAssignment::orderBy('fecha')->orderBy('hora_inicio')->latest()->get()
            : collect();

        $specialGuestReservations = Schema::hasTable('invitados_reserva')
            ? SpecialGuestReservation::with('creador')->orderBy('fecha')->orderBy('hora_inicio')->latest()->get()
            : collect();

        [$reportFechaInicio, $reportFechaFin] = $this->resolveDateRange(request());
        $reportTipoUsuarios = request('tipo_usuarios', 'usuario');
        $reportReservas = Schema::hasTable('reservas')
            ? $this->reservasForRange($reportFechaInicio, $reportFechaFin)
            : collect();
        $reportUsuarios = Schema::hasTable('users')
            ? $this->usersForReport($reportTipoUsuarios)
            : collect();

        /*
        |--------------------------------------------------------------------------
        | CONTENIDO DE BIENVENIDA
        |--------------------------------------------------------------------------
        | Estos datos sirven para que el dashboard cargue el panel interno
        | de edición de Bienvenido sin salir del administrador.
        */

        $settings = WelcomeSetting::pluck('value', 'key');

        $slides = WelcomeSlide::orderBy('position')
            ->get();

        $aboutSections = collect();
        $galleryImages = collect();
        $eventItems = collect();
        $researchItems = collect();

        if (Schema::hasTable('page_sections')) {
            $aboutSections = PageSection::where('page', 'acerca')
                ->orderBy('position')
                ->get()
                ->keyBy('section_key');
        }

        if (Schema::hasTable('gallery_images')) {
            $galleryImages = GalleryImage::orderBy('position')
                ->get();
        }

        if (Schema::hasTable('public_content_items')) {
            $eventItems = PublicContentItem::where('page', 'eventos')
                ->orderBy('position')
                ->get();

            $researchItems = PublicContentItem::where('page', 'investigacion')
                ->orderBy('position')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | ESTADÍSTICAS DE RESERVAS
        |--------------------------------------------------------------------------
        | Se calcula de forma segura para evitar errores si una columna no existe.
        */

        $reservasHoy = 0;
        $visitasConfirmadas = 0;
        $porConfirmar = 0;
        $aforoOcupadoHoy = 0;
        $disponibilidadHoy = now()->isWeekend() ? 'Cerrado' : 'Disponible';
        $cuposLibres = $disponibilidadHoy;
        $chartLabels = [];
        $chartReservas = [];
        $chartVisitantes = [];
        $availabilityDays = collect();
        $selectedDate = request('fecha') ? Carbon::parse(request('fecha')) : Carbon::today();
        $selectedDateReservas = collect();
        $selectedDateStats = [
            'total' => 0,
            'visitantes' => 0,
            'confirmadas' => 0,
            'pendientes' => 0,
        ];
        $pagosConfirmados = 0;
        $recaudacionConfirmada = 0;

        if (class_exists(Reserva::class) && Schema::hasTable('reservas')) {
            $reservasHoy = Reserva::whereDate('fecha', now()->toDateString())
                ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
                ->count();

            $aforoOcupadoHoy = Reserva::whereDate('fecha', now()->toDateString())
                ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
                ->sum('cantidad_personas');

            $reservasActivasSeleccionadas = Reserva::whereDate('fecha', $selectedDate)
                ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
                ->count();

            $disponibilidadHoy = $selectedDate->isWeekend()
                ? 'Cerrado'
                : ($reservasActivasSeleccionadas > 0 ? 'Reservado' : 'Disponible');

            $cuposLibres = $disponibilidadHoy;

            if (Schema::hasColumn('reservas', 'estado')) {
                $porConfirmar = Reserva::whereDate('fecha', now()->toDateString())
                    ->where('estado', 'Pendiente')
                    ->count();

                $visitasConfirmadas = (int) Reserva::whereDate('fecha', now()->toDateString())
                    ->where('estado', 'Confirmado')
                    ->sum('cantidad_personas');
            }

            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
            for ($i = 0; $i < 5; $i++) {
                $date = $weekStart->copy()->addDays($i);
                $chartLabels[] = ucfirst($date->locale('es')->isoFormat('dd'));
                $chartReservas[] = Reserva::whereDate('fecha', $date)->count();
                $chartVisitantes[] = (int) Reserva::whereDate('fecha', $date)
                    ->where('estado', 'Confirmado')
                    ->sum('cantidad_personas');
            }

            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();

            $availabilityDays = collect(CarbonPeriod::create($monthStart, $monthEnd))
                ->map(function (Carbon $date) use ($selectedDate) {
                    $reservas = Reserva::whereDate('fecha', $date)->count();
                    $visitantes = Reserva::whereDate('fecha', $date)
                        ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
                        ->sum('cantidad_personas');
                    $blocked = $date->isWeekend();

                    return [
                        'day' => $date->day,
                        'date' => $date->toDateString(),
                        'selected' => $date->isSameDay($selectedDate),
                        'blocked' => $blocked,
                        'reserved' => $reservas > 0,
                        'full' => false,
                        'reservas' => $reservas,
                        'visitantes' => (int) $visitantes,
                    ];
                });

            $selectedDateReservas = Reserva::with(['user', 'turno', 'horario'])
                ->whereDate('fecha', $selectedDate)
                ->latest()
                ->get();

            $selectedDateStats = [
                'total' => $selectedDateReservas->count(),
                'visitantes' => (int) $selectedDateReservas
                    ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
                    ->sum('cantidad_personas'),
                'confirmadas' => $selectedDateReservas->where('estado', 'Confirmado')->count(),
                'pendientes' => $selectedDateReservas->where('estado', 'Pendiente')->count(),
            ];
        }

        if (class_exists(Pago::class) && Schema::hasTable('pagos')) {
            $pagosConfirmadosQuery = Pago::query()
                ->whereIn('estado_pago', ['Confirmado', 'Completado', 'Pagado', 'Aprobado']);

            if (Schema::hasTable('reservas')) {
                $pagosConfirmadosQuery->whereHas('reserva', function ($query) {
                    $query->where('estado', 'Confirmado');
                });
            }

            $pagosConfirmados = (clone $pagosConfirmadosQuery)->count();
            $recaudacionConfirmada = (float) (clone $pagosConfirmadosQuery)->sum('monto');
        }

        /*
        |--------------------------------------------------------------------------
        | RETORNO AL DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', [
            'totalUsuarios' => $totalUsuarios,
            'usuariosRegistrados' => $usuariosRegistrados,
            'usuariosInternos' => $usuariosInternos,
            'usuariosGestion' => $usuariosGestion,
            'usuariosListaNegra' => $usuariosListaNegra,
            'roles' => $roles,
            'guideAssignments' => $guideAssignments,
            'specialGuestReservations' => $specialGuestReservations,

            'settings' => $settings,
            'slides' => $slides,
            'aboutSections' => $aboutSections,
            'galleryImages' => $galleryImages,
            'eventItems' => $eventItems,
            'researchItems' => $researchItems,

            'reservasHoy' => $reservasHoy,
            'cuposLibres' => $cuposLibres,
            'disponibilidadHoy' => $disponibilidadHoy,
            'porConfirmar' => $porConfirmar,
            'visitasConfirmadas' => $visitasConfirmadas,
            'aforoOcupadoHoy' => $aforoOcupadoHoy,
            'chartLabels' => $chartLabels,
            'chartReservas' => $chartReservas,
            'chartVisitantes' => $chartVisitantes,
            'availabilityDays' => $availabilityDays,
            'selectedDate' => $selectedDate,
            'selectedDateReservas' => $selectedDateReservas,
            'selectedDateStats' => $selectedDateStats,
            'pagosConfirmados' => $pagosConfirmados,
            'recaudacionConfirmada' => $recaudacionConfirmada,
            'eventosPublicados' => $eventItems->where('is_active', true)->count(),
            'invitadosEspecialesCount' => $specialGuestReservations->count(),

            'reportFechaInicio' => $reportFechaInicio,
            'reportFechaFin' => $reportFechaFin,
            'reportPreset' => request('preset', 'mensual'),
            'reportTipoUsuarios' => $reportTipoUsuarios,
            'reportReservas' => $reportReservas,
            'reportUsuarios' => $reportUsuarios,
            'reportReservaStats' => [
                'total' => $reportReservas->count(),
                'usuarios' => $reportReservas->pluck('user_id')->filter()->unique()->count(),
                'visitantes' => (int) $reportReservas->where('estado', 'Confirmado')->sum('cantidad_personas'),
                'pendientes' => $reportReservas->where('estado', 'Pendiente')->count(),
                'confirmadas' => $reportReservas->where('estado', 'Confirmado')->count(),
            ],
            'reportUsuarioStats' => [
                'total' => $reportUsuarios->count(),
                'visitantes' => $reportUsuarios->where('role', 'usuario')->count(),
                'internos' => $reportUsuarios->whereIn('role', ['admin', 'secretaria'])->count(),
            ],
            'maintenanceInfo' => $this->maintenanceInfo(),
        ]);
    }

    public function storeRole(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $nombre = Str::of($data['nombre'])->lower()->ascii()->replace(' ', '_')->toString();

        Role::firstOrCreate(['nombre' => $nombre]);

        return back()->with('success', 'Rol creado correctamente.');
    }

    public function blacklistUser(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.dashboard', ['panel' => 'usuarios'])
                ->with('status', 'No puedes enviarte a lista negra desde tu propia sesión.');
        }

        $data = $request->validate([
            'blacklist_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->forceFill([
            'is_blacklisted' => true,
            'blacklist_reason' => $data['blacklist_reason'] ?? 'Incumplimiento de normas del observatorio.',
            'blacklisted_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.dashboard', ['panel' => 'usuarios'])
            ->with('success', 'Usuario enviado a lista negra correctamente.');
    }

    public function restoreUser(User $user)
    {
        $user->forceFill([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'blacklisted_at' => null,
        ])->save();

        return redirect()
            ->route('admin.dashboard', ['panel' => 'usuarios'])
            ->with('success', 'Usuario restaurado correctamente.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.dashboard', ['panel' => 'usuarios'])
                ->with('status', 'No puedes eliminar tu propia cuenta desde esta pantalla.');
        }

        $user->delete();

        return redirect()
            ->route('admin.dashboard', ['panel' => 'usuarios'])
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function storeSpecialGuestReservation(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'institucion' => ['nullable', 'string', 'max:255'],
            'pais' => ['nullable', 'string', 'max:120'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'tipo_visita' => ['required', 'string', 'max:80'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'cantidad_personas' => ['required', 'integer', 'min:1', 'max:300'],
            'motivo' => ['nullable', 'string', 'max:800'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($data['hora_fin']) && ! empty($data['hora_inicio'])) {
            $data['hora_fin'] = Carbon::createFromFormat('H:i', $data['hora_inicio'])
                ->addMinutes(90)
                ->format('H:i');
        }

        $data['estado'] = 'Confirmado';
        $data['creado_por'] = auth()->id();

        SpecialGuestReservation::create($data);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Reserva especial creada correctamente.');
    }

    public function destroySpecialGuestReservation(SpecialGuestReservation $specialGuestReservation)
    {
        $specialGuestReservation->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Invitado especial eliminado correctamente.');
    }

    public function storeGuideAssignment(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'ci' => ['nullable', 'string', 'max:40'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $fecha = Carbon::parse($data['fecha']);
        if ($fecha->isWeekend()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('status', 'No se puede asignar guía en sábado o domingo.');
        }

        $assignment = GuideAssignment::create($data);
        $message = $this->guideAssignmentMessage($assignment);
        $whatsappUrl = $this->guideWhatsappUrl($assignment, $message);

        if ($assignment->email) {
            try {
                Mail::raw($message, function ($mail) use ($assignment) {
                    $mail->to($assignment->email)
                        ->subject('Asignación de guía - Observatorio Max Schreier');
                });

                $assignment->forceFill([
                    'email_sent_at' => now(),
                ])->save();
            } catch (\Throwable $exception) {
                Log::warning('No se pudo enviar la asignación de guía por correo.', [
                    'guide_assignment_id' => $assignment->id,
                    'email' => $assignment->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($whatsappUrl) {
            $assignment->forceFill([
                'whatsapp_link_generated_at' => now(),
            ])->save();
        }

        $redirect = redirect()
            ->route('admin.dashboard')
            ->with('success', 'Guía asignado correctamente.');

        return $whatsappUrl
            ? $redirect->with('whatsapp_url', $whatsappUrl)
            : $redirect;
    }

    public function destroyGuideAssignment(GuideAssignment $guideAssignment)
    {
        $guideAssignment->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Asignación de guía eliminada correctamente.');
    }

    private function guideAssignmentMessage(GuideAssignment $assignment): string
    {
        $fecha = $assignment->fecha?->format('d/m/Y') ?? 'fecha asignada';
        $hora = trim(
            ($assignment->hora_inicio ? substr($assignment->hora_inicio, 0, 5) : '') .
            ($assignment->hora_fin ? ' - ' . substr($assignment->hora_fin, 0, 5) : '')
        );

        $message = "Hola {$assignment->nombre}, se le asignó una sesión como guía del Observatorio Max Schreier para el {$fecha}";

        if ($hora !== '') {
            $message .= " en el horario {$hora}";
        }

        if ($assignment->observacion) {
            $message .= ". Observación: {$assignment->observacion}";
        }

        return $message . '. Por favor confirmar disponibilidad.';
    }

    private function guideWhatsappUrl(GuideAssignment $assignment, string $message): ?string
    {
        $phone = preg_replace('/\D+/', '', $assignment->telefono ?? '');

        if ($phone === '') {
            return null;
        }

        $phone = str_starts_with($phone, '591') ? $phone : '591' . $phone;

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    public function reservasPdf(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->resolveDateRange($request);
        $reservas = $this->reservasForRange($fechaInicio, $fechaFin);

        $data = [
            'titulo' => 'Reporte administrativo de reservas',
            'rango' => $fechaInicio->format('d/m/Y') . ' al ' . $fechaFin->format('d/m/Y'),
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'reservas' => $reservas,
            'totalReservas' => $reservas->count(),
            'usuariosUnicos' => $reservas->pluck('user_id')->filter()->unique()->count(),
            'confirmadas' => $reservas->where('estado', 'Confirmado')->count(),
            'pendientes' => $reservas->where('estado', 'Pendiente')->count(),
            'canceladas' => $reservas->whereIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])->count(),
            'visitantes' => (int) $reservas->where('estado', 'Confirmado')->sum('cantidad_personas'),
            'logoBase64' => $this->logoBase64(),
        ];

        return Pdf::loadView('admin.reportes.reservas-pdf', $data)
            ->download('reporte_reservas_' . now()->format('Ymd_His') . '.pdf');
    }

    public function usuariosPdf(Request $request)
    {
        $tipo = $request->input('tipo', 'usuario');
        $usuarios = $this->usersForReport($tipo);

        return Pdf::loadView('admin.reportes.usuarios-pdf', [
            'usuarios' => $usuarios,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'totalUsuarios' => $usuarios->count(),
            'usuariosExternos' => $usuarios->where('role', 'usuario')->count(),
            'usuariosInternos' => $usuarios->whereIn('role', ['admin', 'secretaria'])->count(),
            'tipo' => $tipo,
            'logoBase64' => $this->logoBase64(),
        ])->download('reporte_usuarios_' . now()->format('Ymd_His') . '.pdf');
    }

    public function reportes(Request $request)
    {
        return redirect()->route('admin.dashboard', array_merge(
            $request->query(),
            ['panel' => 'reportes']
        ));
    }

    public function downloadDatabaseBackup()
    {
        $sql = $this->buildDatabaseBackupSql();
        $filename = 'backup_observatorio_' . now()->format('Ymd_His') . '.sql';

        Log::info('Backup de base de datos generado por administrador.', [
            'admin_id' => auth()->id(),
            'filename' => $filename,
        ]);

        return response($sql, 200, [
            'Content-Type' => 'application/sql; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function downloadLog()
    {
        $path = storage_path('logs/laravel.log');

        if (! File::exists($path)) {
            return redirect()
                ->route('admin.dashboard', ['panel' => 'mantenimiento'])
                ->with('status', 'No existe archivo de log para descargar todavía.');
        }

        Log::info('Log del sistema descargado por administrador.', [
            'admin_id' => auth()->id(),
        ]);

        return response()->download($path, 'logs_observatorio_' . now()->format('Ymd_His') . '.log');
    }

    public function clearLog()
    {
        $path = storage_path('logs/laravel.log');

        if (File::exists($path)) {
            File::put($path, '');
        }

        Log::info('Log del sistema limpiado desde el panel administrador.', [
            'admin_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.dashboard', ['panel' => 'mantenimiento'])
            ->with('success', 'Log limpiado correctamente.');
    }

    private function isHoliday(Carbon $date): bool
    {
        $fixedHolidays = [
            '01-01',
            '01-22',
            '05-01',
            '08-06',
            '11-02',
            '12-25',
        ];

        return in_array($date->format('m-d'), $fixedHolidays, true);
    }

    private function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'mensual');

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            return [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay(),
            ];
        }

        return match ($preset) {
            'semanal' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'anual' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function reservasForRange(Carbon $fechaInicio, Carbon $fechaFin)
    {
        return Reserva::with(['user', 'turno', 'horario', 'pago'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha')
            ->orderBy('horario_id')
            ->get();
    }

    private function usersForReport(string $tipo)
    {
        return User::query()
            ->when($tipo === 'usuario', fn ($query) => $query->where('role', 'usuario'))
            ->when($tipo === 'internos', fn ($query) => $query->whereIn('role', ['admin', 'secretaria']))
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    }

    private function maintenanceInfo(): array
    {
        $logPath = storage_path('logs/laravel.log');
        $logExists = File::exists($logPath);
        $logSize = $logExists ? File::size($logPath) : 0;
        $database = config('database.connections.' . config('database.default') . '.database');

        return [
            'database' => $database,
            'driver' => DB::connection()->getDriverName(),
            'generated_at' => now()->format('d/m/Y H:i'),
            'log_exists' => $logExists,
            'log_size' => $this->humanFileSize($logSize),
            'log_size_bytes' => $logSize,
            'log_modified' => $logExists ? Carbon::createFromTimestamp(File::lastModified($logPath))->format('d/m/Y H:i') : 'Sin registros',
            'log_lines' => $logExists ? $this->tailFile($logPath, 24) : [],
            'storage_writable' => is_writable(storage_path()),
            'backup_ready' => true,
        ];
    }

    private function buildDatabaseBackupSql(): string
    {
        $driver = DB::connection()->getDriverName();
        $database = config('database.connections.' . config('database.default') . '.database');
        $tables = $this->databaseTables($driver);

        $sql = [
            '-- Backup Observatorio Max Schreier',
            '-- Generado: ' . now()->format('Y-m-d H:i:s'),
            '-- Base de datos: ' . $database,
            '-- Driver: ' . $driver,
            '',
            'SET FOREIGN_KEY_CHECKS=0;',
            '',
        ];

        foreach ($tables as $table) {
            $sql[] = '-- --------------------------------------------------------';
            $sql[] = '-- Tabla: ' . $table;
            $sql[] = '-- --------------------------------------------------------';
            $sql[] = 'DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table, $driver) . ';';
            $sql[] = $this->createTableSql($table, $driver) . ';';
            $sql[] = '';

            DB::table($table)->orderByRaw('1')->chunk(300, function ($rows) use (&$sql, $table, $driver) {
                foreach ($rows as $row) {
                    $data = (array) $row;
                    $columns = collect(array_keys($data))
                        ->map(fn ($column) => $this->quoteIdentifier($column, $driver))
                        ->implode(', ');
                    $values = collect(array_values($data))
                        ->map(fn ($value) => $this->sqlValue($value))
                        ->implode(', ');

                    $sql[] = 'INSERT INTO ' . $this->quoteIdentifier($table, $driver) . ' (' . $columns . ') VALUES (' . $values . ');';
                }
            });

            $sql[] = '';
        }

        $sql[] = 'SET FOREIGN_KEY_CHECKS=1;';
        $sql[] = '';

        return implode(PHP_EOL, $sql);
    }

    private function databaseTables(string $driver): array
    {
        if ($driver === 'mysql') {
            return collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"))
                ->map(fn ($row) => array_values((array) $row)[0] ?? null)
                ->filter()
                ->values()
                ->all();
        }

        if ($driver === 'sqlite') {
            return collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"))
                ->pluck('name')
                ->values()
                ->all();
        }

        return collect(Schema::getTables())
            ->map(fn ($table) => $table['name'] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    private function createTableSql(string $table, string $driver): string
    {
        if ($driver === 'mysql') {
            $row = (array) DB::select('SHOW CREATE TABLE ' . $this->quoteIdentifier($table, $driver))[0];

            return $row['Create Table'] ?? array_values($row)[1] ?? '';
        }

        if ($driver === 'sqlite') {
            $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);

            return $row->sql ?? '';
        }

        return '-- CREATE TABLE no disponible automaticamente para este driver: ' . $table;
    }

    private function quoteIdentifier(string $identifier, string $driver): string
    {
        $quote = $driver === 'sqlite' ? '"' : '`';

        return $quote . str_replace($quote, $quote . $quote, $identifier) . $quote;
    }

    private function sqlValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return DB::connection()->getPdo()->quote((string) $value);
    }

    private function tailFile(string $path, int $lines = 20): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $rows = preg_split('/\R/', trim(File::get($path)));

        return array_values(array_filter(array_slice($rows ?: [], -$lines)));
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    private function logoBase64(): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = public_path('images/observatorio-logo.png');

        if (! file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
