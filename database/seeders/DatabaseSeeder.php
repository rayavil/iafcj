<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@iafcj.org'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('iafcj2026'),
            ]
        );

        Evento::firstOrCreate(
            ['nombre' => 'Evento de prueba'],
            [
                'fecha' => now(),
                'activo' => true,
            ]
        );
    }
}
