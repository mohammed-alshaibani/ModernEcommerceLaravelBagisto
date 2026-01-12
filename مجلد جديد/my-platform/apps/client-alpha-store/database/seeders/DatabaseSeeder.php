<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User for Filament
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'المدير العام',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // 2. Seed 10 High-End Arabic Products
        $products = [
            ['عطر العود الكمبودي', 'رائحة أصيلة تدوم طويلاً، مستخلص من خشب العود المعتق.', 450.00, 'OUD-001'],
            ['ساعة ألماس نسائية', 'تصميم فاخر مرصع بالألماس الصناعي عالي الجودة.', 1200.00, 'WCH-002'],
            ['بشت حساوي فاخر', 'بشت يدوي الصنع، خامة صوف ناعمة للمناسبات الرسمية.', 850.00, 'BST-003'],
            ['طقم قهوة عربي', 'طقم بورسلان بتصميم تراثي مع نقوش ذهبية.', 180.00, 'CFE-004'],
            ['مسبحة يسر مطعمة', 'مسبحة يدوية من اليسر الطبيعي مطعمة بالفضة.', 300.00, 'MSB-005'],
            ['دهن ورد طائفي', 'تولة دهن ورد طائفي قطفة أولى.', 250.00, 'ROS-006'],
            ['شماغ أحمر كلاسيك', 'شماغ قطني 100% صناعة إنجليزية.', 120.00, 'SHM-007'],
            ['فستان سهرة مطرز', 'فستان سهرة بتصميم عصري وتطريز يدوي.', 900.00, 'DRS-008'],
            ['خاتم فضة عقيق', 'خاتم فضة عيار 925 بحجر العقيق اليماني.', 150.00, 'RNG-009'],
            ['شنطة جلد طبيعي', 'حقيبة يد نسائية مصنوعة من الجلد الطبيعي الفاخر.', 550.00, 'BAG-010'],
        ];

        foreach ($products as $index => $item) {
            Product::create([
                'name' => $item[0],
                'description' => $item[1],
                'price' => $item[2],
                'sku' => $item[3],
                'stock' => 100, // Default stock
            ]);
        }
    }
}
