<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turno;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 1. Cargamos las reservas del usuario (Necesario para evitar errores en la vista)
        $misReservas = Reserva::where('user_id', $user->id)->with(['turno', 'horario'])->latest()->get();
        $proximasReservas = Reserva::where('user_id', $user->id)
            ->where('estado', 'Confirmado')
            ->where('fecha', '>=', Carbon::now()->toDateString())
            ->with(['turno', 'horario'])->limit(5)->get();

        // 2. ESTADÍSTICAS: Aquí agregué la clave 'canceladas' que faltaba
        $estadisticas = [
            'total_reservas' => Reserva::where('user_id', $user->id)->count(),
            'confirmadas'   => Reserva::where('user_id', $user->id)->where('estado', 'Confirmado')->count(),
            'pendientes'    => Reserva::where('user_id', $user->id)->where('estado', 'Pendiente')->count(),
            'canceladas'    => Reserva::where('user_id', $user->id)->where('estado', 'Cancelada')->count(), // ¡SOLUCIONADO!
        ];
        
        // 3. Datos para el Panel de Admin
        $usuariosInternos = User::whereIn('role', ['Admin', 'Secretaría', 'secretaria'])->get();
        $totalUsuarios = User::count();
        $reservasHoy = Reserva::whereDate('fecha', Carbon::today())->count();
        $turnosDisponibles = Turno::all();
        $turnos = Turno::with('horarios')->where('activo', true)->get();
        $reservas = $misReservas;
        $totalReservas = $estadisticas['total_reservas'];
        $confirmadas = $estadisticas['confirmadas'];
        $pendientes = $estadisticas['pendientes'];
        $canceladas = $estadisticas['canceladas'];

        return view('usuario.dashboard', compact(
            'misReservas', 'proximasReservas', 'estadisticas', 
            'turnosDisponibles', 'usuariosInternos', 'totalUsuarios', 'reservasHoy',
            'turnos', 'reservas', 'totalReservas', 'confirmadas', 'pendientes', 'canceladas'
        ));
    }

    /**
     * REGISTRO DIRECTO DESDE EL PANEL
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'carnet_identidad' => 'required',
            'telefono' => 'nullable|string|max:20',
        ]);

        $parts = preg_split('/\s+/', trim($request->name), 2);
        $name = $parts[0] ?? $request->name;
        $apellido = $parts[1] ?? 'Interno';

        // Creamos el usuario con su rol y contraseña genérica (su carnet)
        User::create([
            'name' => $name,
            'apellido' => $apellido,
            'ci' => $request->carnet_identidad,
            'telefono' => $request->telefono ?? 'No especificado',
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->carnet_identidad), 
            'id_acceso' => $request->carnet_identidad,
            'departamento' => $request->role === 'admin' ? 'Administración' : 'Secretaría',
        ]);

        // Volvemos a la misma página. Ahora la lista se actualizará sola.
        return redirect()
            ->route('admin.dashboard', ['panel' => 'usuarios'])
            ->with('success', '¡Usuario creado!');
    }
}
