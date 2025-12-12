<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun Admin pasti
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@toko.com',
            'password' => bcrypt('password123'),
        ]);

        // (Opsional) Tampilkan pesan di terminal
        $this->command->info('Akun Admin berhasil dibuat!');
    }
}
