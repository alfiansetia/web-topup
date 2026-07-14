<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin12345'),
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'User nih',
            'email' => 'user@gmail.com',
            'password' => bcrypt('user12345'),
            'role' => 'user',
        ]);
        User::factory()->create([
            'name' => 'Blocked User',
            'email' => 'blocked@gmail.com',
            'password' => bcrypt('blocked123'),
            'role' => 'user',
            'is_blocked' => true,
        ]);

        // ── Categories ──
        $categories = [
            [
                'name' => 'Streaming',
                'description' => 'Akun layanan streaming video & musik',
                'sort_order' => 1,
                'products' => [
                    [
                        'name' => 'YouTube Premium',
                        'description' => 'Nikmati YouTube tanpa iklan, background play, dan YouTube Music Premium.',
                        'checkout_instruction' => 'Cantumkan email akun YouTube Anda di kolom keterangan.',
                        'features' => ['Tanpa Iklan', 'Background Play', 'YouTube Music', 'Download Offline'],
                        'sort_order' => 1,
                        'variants' => [
                            ['name' => '1 Bulan', 'price' => 15000, 'discount_price' => 12000, 'sort_order' => 1],
                            ['name' => '3 Bulan', 'price' => 40000, 'discount_price' => 35000, 'sort_order' => 2],
                            ['name' => '6 Bulan', 'price' => 75000, 'sort_order' => 3],
                            ['name' => '1 Tahun', 'price' => 120000, 'discount_price' => 99000, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'name' => 'Netflix Premium',
                        'description' => 'Tonton film dan series Netflix resolusi 4K di 4 perangkat sekaligus.',
                        'checkout_instruction' => 'Masukkan email dan password akun Netflix pada kolom keterangan.',
                        'features' => ['4K Ultra HD', '4 Layar Bersamaan', 'Download Offline', 'Semua Konten'],
                        'sort_order' => 2,
                        'variants' => [
                            ['name' => '1 Bulan (Shared)', 'price' => 25000, 'sort_order' => 1],
                            ['name' => '1 Bulan (Private)', 'price' => 55000, 'discount_price' => 45000, 'sort_order' => 2],
                            ['name' => '3 Bulan (Private)', 'price' => 140000, 'sort_order' => 3],
                        ],
                    ],
                    [
                        'name' => 'Spotify Premium',
                        'description' => 'Dengarkan musik tanpa iklan dengan kualitas audio tinggi.',
                        'features' => ['Tanpa Iklan', 'Audio HD', 'Download Offline', 'Skip Unlimited'],
                        'sort_order' => 3,
                        'variants' => [
                            ['name' => '1 Bulan', 'price' => 15000, 'discount_price' => 10000, 'sort_order' => 1],
                            ['name' => '3 Bulan', 'price' => 40000, 'sort_order' => 2],
                            ['name' => '6 Bulan', 'price' => 70000, 'sort_order' => 3],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Gaming',
                'description' => 'Akun game dan voucher top-up',
                'sort_order' => 2,
                'products' => [
                    [
                        'name' => 'Mobile Legends',
                        'description' => 'Diamond Mobile Legends harga termurah dan proses cepat.',
                        'checkout_instruction' => 'Cantumkan User ID dan Zone ID game Anda di kolom keterangan.',
                        'features' => ['Proses Otomatis', 'Harga Termurah', 'Aman 100%'],
                        'sort_order' => 1,
                        'variants' => [
                            ['name' => '56 Diamond', 'price' => 15000, 'sort_order' => 1],
                            ['name' => '172 Diamond', 'price' => 45000, 'discount_price' => 42000, 'sort_order' => 2],
                            ['name' => '344 Diamond', 'price' => 85000, 'sort_order' => 3],
                            ['name' => '706 Diamond', 'price' => 165000, 'discount_price' => 155000, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'name' => 'Free Fire',
                        'description' => 'Diamond Free Fire untuk beli skin dan bundle favorit.',
                        'features' => ['Proses Cepat', 'Harga Bersahabat'],
                        'sort_order' => 2,
                        'variants' => [
                            ['name' => '70 Diamond', 'price' => 10000, 'sort_order' => 1],
                            ['name' => '140 Diamond', 'price' => 20000, 'sort_order' => 2],
                            ['name' => '355 Diamond', 'price' => 50000, 'discount_price' => 48000, 'sort_order' => 3],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Produktivitas',
                'description' => 'Akun aplikasi produktivitas dan tools',
                'sort_order' => 3,
                'products' => [
                    [
                        'name' => 'Microsoft 365',
                        'description' => 'Akses Word, Excel, PowerPoint, dan 1TB OneDrive.',
                        'checkout_instruction' => 'Tulis nama lengkap untuk aktivasi akun Microsoft 365 Anda.',
                        'features' => ['Word, Excel, PowerPoint', '1TB OneDrive', 'Email Outlook', '1 Akun 5 Device'],
                        'sort_order' => 1,
                        'variants' => [
                            ['name' => '1 Bulan', 'price' => 25000, 'sort_order' => 1],
                            ['name' => '1 Tahun', 'price' => 150000, 'discount_price' => 125000, 'sort_order' => 2],
                        ],
                    ],
                    [
                        'name' => 'Canva Pro',
                        'description' => 'Desain profesional dengan ribuan template premium.',
                        'features' => ['Template Premium', 'Background Remover', 'Brand Kit', '100GB Storage'],
                        'sort_order' => 2,
                        'variants' => [
                            ['name' => '1 Bulan', 'price' => 15000, 'discount_price' => 12000, 'sort_order' => 1],
                            ['name' => '1 Tahun', 'price' => 80000, 'sort_order' => 2],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $products = $catData['products'];
            unset($catData['products']);

            $category = Category::create($catData);

            foreach ($products as $prodData) {
                $variants = $prodData['variants'];
                unset($prodData['variants']);

                $product = $category->products()->create($prodData);

                foreach ($variants as $varData) {
                    $variant = $product->variants()->create(array_merge($varData, [
                        'stock_count' => 0,
                    ]));

                    // Generate 5 dummy stock items per variant
                    for ($i = 1; $i <= 5; $i++) {
                        $variant->items()->create([
                            'product_id' => $product->id,
                            'content' => "akun{$category->id}{$product->id}{$variant->id}_{$i}@example.com|Password{$i}!",
                            'status' => 'available',
                        ]);
                    }

                    // Update stock_count
                    $variant->update(['stock_count' => $variant->items()->where('status', 'available')->count()]);
                }
            }
        }
    }
}
