<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DevCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $skinCare = Category::query()->create([
            'name' => 'Chăm sóc da',
            'slug' => 'cham-soc-da',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        foreach ([
            ['name' => 'Sữa rửa mặt', 'slug' => 'sua-rua-mat', 'sort_order' => 1],
            ['name' => 'Serum', 'slug' => 'serum', 'sort_order' => 2],
            ['name' => 'Kem dưỡng', 'slug' => 'kem-duong', 'sort_order' => 3],
        ] as $category) {
            Category::query()->create($category + [
                'parent_id' => $skinCare->id,
                'is_active' => true,
            ]);
        }

        Category::query()->create([
            'name' => 'Trang điểm',
            'slug' => 'trang-diem',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        foreach ([
            ['Anessa', 'anessa'],
            ['La Roche-Posay', 'la-roche-posay'],
            ['L’Oréal Paris', 'loreal-paris'],
            ['Maybelline', 'maybelline'],
            ['Vichy', 'vichy'],
        ] as [$name, $slug]) {
            Brand::query()->create([
                'name' => $name,
                'slug' => $slug,
                'logo_url' => "https://placehold.co/300x150?text={$slug}",
                'banner_image' => "https://placehold.co/1200x400?text={$slug}",
                'description' => "Gian hàng chính hãng {$name} tại Mizuki.",
                'is_active' => true,
            ]);
        }
    }
}
