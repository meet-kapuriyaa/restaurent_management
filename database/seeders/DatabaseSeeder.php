<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Table;
use App\Models\FoodItem;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Tables
        $tables = [
            ['table_number' => 'T1', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'T2', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'T3', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'T4', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => 'T5', 'capacity' => 8, 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::firstOrCreate(['table_number' => $table['table_number']], $table);
        }

        // Seed Food Items
        $foodItems = [
            [
                'name' => 'Classic Steamed Idli (2 Pcs)',
                'price' => 40.00,
                'description' => 'Soft, fluffy steamed rice cakes served with signature coconut chutney, tomato chutney, and piping hot sambar.',
                'status' => 'available'
            ],
            [
                'name' => 'Ghee Podi Idli',
                'price' => 60.00,
                'description' => 'Spongy idlis tossed in pure cow ghee and aromatic South Indian gunpowder (podi) spice mix.',
                'status' => 'available'
            ],
            [
                'name' => 'Mini Coin Idli',
                'price' => 50.00,
                'description' => 'Bite-sized, pillowy soft button idlis drenched in aromatic sambar and fresh coconut chutney.',
                'status' => 'available'
            ],
            [
                'name' => 'Classic Masala Dosa',
                'price' => 90.00,
                'description' => 'Golden crispy rice crepe stuffed with spiced potato mash, served with coconut chutney and hot sambar.',
                'status' => 'available'
            ],
            [
                'name' => 'Mysore Masala Dosa',
                'price' => 100.00,
                'description' => 'Crispy dosa spread with a spicy red garlic chutney lining and seasoned potato filling.',
                'status' => 'available'
            ],
            [
                'name' => 'Rava Onion Dosa',
                'price' => 100.00,
                'description' => 'Crispy, lacy semolina crepe loaded with chopped onions, cumin seeds, and fresh green chillies.',
                'status' => 'available'
            ],
            [
                'name' => 'Medu Vada (2 Pcs)',
                'price' => 40.00,
                'description' => 'Golden-fried black gram donuts seasoned with peppercorns, ginger, and curry leaves.',
                'status' => 'available'
            ],
            [
                'name' => 'Filter Coffee',
                'price' => 30.00,
                'description' => 'Authentic hot South Indian decoction coffee brewed with milk and served in a traditional brass dabarah cup.',
                'status' => 'available'
            ],
            [
                'name' => 'Sweet Kesari Bath',
                'price' => 40.00,
                'description' => 'Traditional semolina dessert pudding cooked with clarified butter, cardamom, and roasted cashews.',
                'status' => 'unavailable'
            ]
        ];

        foreach ($foodItems as $item) {
            FoodItem::firstOrCreate(['name' => $item['name']], $item);
        }

        // Seed Users with roles
        $users = [
            [
                'name' => 'Restaurant Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Waiter Terminal User',
                'email' => 'waiter@example.com',
                'password' => Hash::make('password'),
                'role' => 'waiter',
            ],
            [
                'name' => 'Chef Kitchen User',
                'email' => 'chef@example.com',
                'password' => Hash::make('password'),
                'role' => 'chef',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // Seed Role Permissions matrix
        $permissions = [
            // Admin Permissions
            ['role' => 'admin', 'page' => 'waiter_terminal', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'kitchen_terminal', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'admin_panel', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'can_insert', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'can_update', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'can_delete', 'is_allowed' => true],

            // Waiter Permissions
            ['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true],
            ['role' => 'waiter', 'page' => 'kitchen_terminal', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'admin_panel', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_insert', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_update', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_delete', 'is_allowed' => false],

            // Chef Permissions
            ['role' => 'chef', 'page' => 'waiter_terminal', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true],
            ['role' => 'chef', 'page' => 'admin_panel', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_insert', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_update', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_delete', 'is_allowed' => false],
        ];

        foreach ($permissions as $perm) {
            RolePermission::updateOrCreate(
                ['role' => $perm['role'], 'page' => $perm['page']],
                ['is_allowed' => $perm['is_allowed']]
            );
        }
    }
}
