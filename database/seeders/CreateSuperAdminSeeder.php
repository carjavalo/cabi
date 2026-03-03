<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'name' => 'Carlos Jairton',
            'apellido1' => 'Valderrama',
            'apellido2' => 'Orobio',
            'identificacion' => '94529371',
            'servicio' => 'Gestion de la Informacion',
            'tipo_vinculacion' => 'Agesoc',
            'email' => 'carjavalosistem@gmail.com',
            'role' => 'Super Admin',
            'password' => Hash::make('jairton7812'),
        ];

        User::updateOrCreate(
            ['email' => $data['email']],
            $data
        );
    }
}
