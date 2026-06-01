<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;

class SecretariaReservaController extends Controller
{
    // Listar todas las reservas realizadas por los usuarios
    public function index()
    {
        // Trae las reservas ordenadas por las más recientes, cargando sus relaciones
        $reservas = Reserva::with(['usuario.persona', 'horario'])->latest()->paginate(10);
        
        return view('admin.secretaria.reservas.index', compact('reservas'));
    }
}