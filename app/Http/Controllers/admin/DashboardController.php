<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. SI ES ADMINISTRADOR
        if ($user->role === 'admin') {
            return view('admin.dashboard'); 
        }

        // 2. SI ES SECRETARIA
        if ($user->role === 'secretaria') {
            // Ella necesita ver todas las reservas para confirmar
            $reservas = Reserva::with('user')->orderBy('fecha', 'desc')->get();
            return view('secretaria.dashboard', compact('reservas'));
        }

        // 3. SI ES USUARIO GENÉRICO (Tu código original)
        // Solo aquí cargamos los turnos para el modal de reserva
        $turnos = Turno::all(); 
        $misReservas = Reserva::where('user_id', $user->id)->get();

        return view('usuario.dashboard', compact('turnos', 'misReservas'));

        //
        $user = auth()->user();
    
    // Buscamos si tiene alguna reserva en estado 'Pendiente'
    $cambioPendiente = Reserva::where('user_id', $user->id)
        ->where('estado', 'Pendiente')
        ->latest()
        ->first();

    return view('dashboard', compact('cambioPendiente', 'user'));
    }
}

