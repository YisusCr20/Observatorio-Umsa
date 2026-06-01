<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;    // ¡CRÍTICO! Faltaba importar este modelo en tu código
use App\Models\Reserva; // Importamos Reserva para actualizar su estado
use App\Notifications\ReservaConfirmada; // También notificamos si paga en ventanilla
use Illuminate\Http\Request;

class SecretariaPagoController extends Controller
{
    /**
     * Registrar un pago de forma manual desde el formulario de secretaría (Punto 4)
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario de manera estricta
        $request->validate([
            'reserva_id'      => 'required|exists:reservas,id_reserva',
            'monto'           => 'required|numeric|min:0',
            'metodo_pago'     => 'required|string',
            'comprobante_url' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $datos = $request->all();

        // Procesar y almacenar el comprobante físico/digital en la carpeta pública
        if ($request->hasFile('comprobante_url')) {
            $ruta = $request->file('comprobante_url')->store('comprobantes', 'public');
            $datos['comprobante_url'] = $ruta;
        }

        // Al ser procesado manualmente por secretaría, el estado se consolida de inmediato
        $datos['estado_pago'] = 'Completado';

        // Crear el registro en la tabla pagos (Esto actualiza la BD y la clase de reportes al instante)
        Pago::create($datos);

        // Actualizar automáticamente el estado de la reserva vinculada
        $reserva = Reserva::findOrFail($request->reserva_id);
        $reserva->update(['estado_reserva' => 'Confirmado']);

        // Opcional: Notificar al usuario que su pago manual validó su reserva
        if ($reserva->usuario && $reserva->usuario->persona) {
            $reserva->usuario->persona->notify(new ReservaConfirmada($reserva));
        }

        return redirect()->back()->with('success', 'Pago registrado con éxito. El historial y reportes se han actualizado.');
    }
}