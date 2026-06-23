<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@presensi.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@presensi.com',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('Admin default berhasil dibuat: admin@presensi.com / admin123');
    }
}
