<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar que sea admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $hoy = Carbon::now()->toDateString();
        
        // Estadísticas principales
        $reservasHoy = Reserva::whereDate('fecha', $hoy)->count();
        $reservasPendientes = Reserva::where('estado', 'Pendiente')->count();
        $totalReservas = Reserva::count();
        $usuariosRegistrados = User::where('role', 'usuario')->count();
        
        // Cálculo de capacidad
        $turnos = Turno::all();
        $capacidadTotal = $turnos->sum('capacidad_maxima');
        $ocupadoHoy = Reserva::whereDate('fecha', $hoy)
            ->where('estado', '!=', 'Cancelada')
            ->sum('cantidad_personas');
        $disponible = $capacidadTotal - $ocupadoHoy;
        
        // Reservas recientes
        $reservasRecientes = Reserva::with(['user', 'turno'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Estadísticas por estado
        $estadisticas = [
            'confirmadas' => Reserva::where('estado', 'Confirmado')->count(),
            'pendientes' => Reserva::where('estado', 'Pendiente')->count(),
            'canceladas' => Reserva::where('estado', 'Cancelada')->count(),
            'rechazadas' => Reserva::where('estado', 'Rechazada')->count(),
        ];

        $usuariosInternos = User::whereIn('role', ['admin', 'secretaria'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'reservasHoy',
            'reservasPendientes',
            'totalReservas',
            'usuariosRegistrados',
            'capacidadTotal',
            'ocupadoHoy',
            'disponible',
            'turnos',
            'reservasRecientes',
            'estadisticas',
            'usuariosInternos'
        ));
    }

    public function storeUser(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'id_acceso' => ['required', 'string', 'max:50', 'unique:users,id_acceso'],
            'role' => ['required', 'in:admin,secretaria'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'id_acceso' => $data['id_acceso'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Cuenta interna creada correctamente.');
    }
}
