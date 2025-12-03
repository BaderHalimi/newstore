<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'العناية بالبشرة',
                'slug' => 'skin-care',
                'description' => 'منتجات العناية بالبشرة والترطيب',
            ],
            [
                'name' => 'المكياج',
                'slug' => 'makeup',
                'description' => 'مستحضرات التجميل والمكياج',
            ],
            [
                'name' => 'العطور',
                'slug' => 'perfumes',
                'description' => 'عطور نسائية ورجالية',
            ],
            [
                'name' => 'العناية بالشعر',
                'slug' => 'hair-care',
                'description' => 'منتجات العناية بالشعر',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            // Create Products for each category
            $products = $this->getProductsForCategory($category->name);

            foreach ($products as $productData) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'] ?? null,
                    'stock' => rand(10, 100),
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'is_active' => true,
                    'is_featured' => rand(0, 1) == 1,
                ]);
            }
        }
    }

    private function getProductsForCategory($categoryName)
    {
        $products = [
            'العناية بالبشرة' => [
                ['name' => 'كريم ترطيب للوجه', 'description' => 'كريم مرطب غني بالفيتامينات للبشرة الجافة', 'price' => 250000, 'sale_price' => 200000],
                ['name' => 'سيروم فيتامين C', 'description' => 'سيروم مضاد للأكسدة يفتح البشرة', 'price' => 300000],
                ['name' => 'واقي شمس SPF 50', 'description' => 'حماية عالية من أشعة الشمس', 'price' => 180000, 'sale_price' => 150000],
                ['name' => 'ماسك الطين', 'description' => 'ماسك منقي للبشرة الدهنية', 'price' => 120000],
            ],
            'المكياج' => [
                ['name' => 'أحمر شفاه مات', 'description' => 'أحمر شفاه طويل الأمد بملمس مخملي', 'price' => 150000],
                ['name' => 'كريم أساس', 'description' => 'تغطية كاملة وطبيعية', 'price' => 280000, 'sale_price' => 230000],
                ['name' => 'ماسكارا مقاومة للماء', 'description' => 'رموش كثيفة وطويلة', 'price' => 180000],
                ['name' => 'باليت ظلال عيون', 'description' => '12 لون متنوع', 'price' => 320000],
            ],
            'العطور' => [
                ['name' => 'عطر زهري فاخر', 'description' => 'عطر نسائي بنفحات زهرية منعشة', 'price' => 450000, 'sale_price' => 380000],
                ['name' => 'عطر رجالي كلاسيكي', 'description' => 'عطر رجالي قوي وجذاب', 'price' => 500000],
                ['name' => 'عطر عود ملكي', 'description' => 'عطر شرقي بخلاصة العود الطبيعي', 'price' => 650000, 'sale_price' => 550000],
            ],
            'العناية بالشعر' => [
                ['name' => 'شامبو مغذي', 'description' => 'شامبو بخلاصة الأرغان للشعر التالف', 'price' => 180000],
                ['name' => 'بلسم مرطب', 'description' => 'بلسم عميق للشعر الجاف', 'price' => 160000, 'sale_price' => 130000],
                ['name' => 'سيروم الشعر', 'description' => 'سيروم لإصلاح الأطراف المتقصفة', 'price' => 220000],
                ['name' => 'ماسك الشعر', 'description' => 'ماسك مكثف للعناية الأسبوعية', 'price' => 200000],
            ],
        ];

        return $products[$categoryName] ?? [];
    }
}
