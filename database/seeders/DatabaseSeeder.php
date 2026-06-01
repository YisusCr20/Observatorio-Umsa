<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Turno;
use App\Models\Reserva;
use App\Models\Horario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Crear usuarios de prueba (AÑADIDOS APELLIDO Y CI)
        $admin = User::updateOrCreate(
            ['email' => 'admin@observatorio.com'],
            [
                'name' => 'Admin',
                'apellido' => 'Usuario', // <--- IMPORTANTE
                'ci' => '1234567',        // <--- IMPORTANTE
                'password' => Hash::make('password'),
                'role' => 'admin',
                'id_acceso' => 'ADM001',
                'telefono' => '591-1-1234567',
                'departamento' => 'Administración',
            ]
        );

        $secretaria = User::updateOrCreate(
            ['email' => 'secretaria@observatorio.com'],
            [
                'name' => 'Secretaria',
                'apellido' => 'Test',    // <--- IMPORTANTE
                'ci' => '7654321',        // <--- IMPORTANTE
                'password' => Hash::make('password'),
                'role' => 'secretaria',
                'id_acceso' => 'SEC001',
                'telefono' => '591-1-2345678',
                'departamento' => 'Secretaría',
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "usuario$i@example.com"],
                [
                    'name' => "Usuario $i",
                    'apellido' => "Apellido $i", // <--- IMPORTANTE
                    'ci' => "100000$i",           // <--- IMPORTANTE
                    'password' => Hash::make('password'),
                    'role' => 'usuario',
                    'id_acceso' => "USR" . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'telefono' => '591-1-' . rand(1000000, 9999999),
                    'departamento' => 'Visitante',
                ]
            );
        }

        // 2. Crear Turnos y sus Horarios
        $turnosData = [
            [
                'nombre' => 'Turno Mañana',
                'hora_inicio' => '09:00',
                'hora_fin' => '12:00',
                'horarios' => ['09:00', '10:00', '11:00']
            ],
            [
                'nombre' => 'Turno Tarde',
                'hora_inicio' => '14:00',
                'hora_fin' => '17:00',
                'horarios' => ['14:00', '15:00', '16:00']
            ],
            [
                'nombre' => 'Turno Noche',
                'hora_inicio' => '18:00',
                'hora_fin' => '21:00',
                'horarios' => ['18:00', '19:00', '20:00']
            ],
        ];

        foreach ($turnosData as $t) {
            $turno = Turno::updateOrCreate(
                ['nombre' => $t['nombre']],
                [
                    'hora_inicio' => $t['hora_inicio'],
                    'hora_fin' => $t['hora_fin'],
                    'capacidad_maxima' => 30,
                    'descripcion' => 'Visitas ' . $t['nombre'],
                    'activo' => true,
                ]
            );

            foreach ($t['horarios'] as $hora) {
                Horario::updateOrCreate(
                    ['turno_id' => $turno->id, 'hora_inicio' => $hora],
                    ['hora_fin' => Carbon::parse($hora)->addHour()->format('H:i')]
                );
            }
        }

        // 3. Crear reservas de prueba
        if (Reserva::count() === 0) {
            $usuarios = User::where('role', 'usuario')->get();
            $turnos = Turno::with('horarios')->get();

            foreach ($usuarios as $usuario) {
                for ($j = 0; $j < 2; $j++) {
                    $turno = $turnos->random();
                    $horario = $turno->horarios->random();

                    Reserva::create([
                        'user_id' => $usuario->id,
                        'turno_id' => $turno->id,
                        'horario_id' => $horario->id,
                        'nombre' => $usuario->name,
                        'correo' => $usuario->email,
                        'telefono' => $usuario->telefono,
                        'fecha' => Carbon::now()->addDays(rand(1, 15))->toDateString(),
                        'cantidad_personas' => rand(1, 5),
                        'estado' => ['Confirmado', 'Pendiente', 'Cancelada'][rand(0, 2)],
                        'descripcion' => 'Reserva de prueba creada por Seeder',
                    ]);
                }
            }
        }
    }
}