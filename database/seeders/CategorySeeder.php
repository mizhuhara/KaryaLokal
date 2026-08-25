<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Bucket Bunga',
            'Bouquet Wisuda',
            'Bucket Uang',
            'Bucket Snack',
            'Bouquet Boneka',
            'Hampers',
            'Gift Box',
            'Crochet / Rajutan',
            'Macrame',
            'Resin',
            'Clay',
            'Kerajinan Kayu',
            'Dekorasi',
            'Souvenir',
            'Wedding Handmade',
            'Custom Handmade',
        ];

        foreach ($categories as $name) {
            Category::query()->firstOrCreate(
                ['slug' => str()->slug($name)],
                ['name' => $name, 'is_active' => true],
            );
        }
    }
}
