<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menambahkan kategori umum toko
        $kategori = [
            'Makanan',
            'Minuman',
            'Sembako',
            'Alat Tulis',
            'Perlengkapan Mandi',
            'Obat-obatan',
            'Elektronik',
            'Rokok',
            'Peralatan Rumah Tangga',
            'Lain-lain'
        ];

        foreach ($kategori as $nama) {
            Category::create([
                'name' => $nama
            ]);
        }
    }
}
