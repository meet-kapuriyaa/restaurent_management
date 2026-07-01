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
                'name' => 'Margherita Pizza',
                'price' => 12.99,
                'description' => 'Classic pizza with fresh mozzarella, tomatoes, and basil leaves.',
                'status' => 'available'
            ],
            [
                'name' => 'Truffle Mushroom Burger',
                'price' => 15.50,
                'description' => 'Gourmet beef patty with truffle mayo, Swiss cheese, and sauteed mushrooms.',
                'status' => 'available'
            ],
            [
                'name' => 'Caesar Salad',
                'price' => 9.99,
                'description' => 'Crisp romaine lettuce, garlic croutons, parmesan cheese, and house Caesar dressing.',
                'status' => 'available'
            ],
            [
                'name' => 'Spaghetti Carbonara',
                'price' => 14.25,
                'description' => 'Creamy pasta sauce, crispy pancetta, black pepper, and shaved parmesan.',
                'status' => 'available'
            ],
            [
                'name' => 'Chocolate Lava Cake',
                'price' => 7.99,
                'description' => 'Warm chocolate cake with a molten chocolate center, served with vanilla ice cream.',
                'status' => 'available'
            ],
            [
                'name' => 'Iced Caramel Macchiato',
                'price' => 4.50,
                'description' => 'Espresso with cold milk, vanilla syrup, and sweet caramel drizzle.',
                'status' => 'available'
            ],
            [
                'name' => 'Mango Smoothie',
                'price' => 5.25,
                'description' => 'Refreshing blend of fresh sweet mangoes and chilled yogurt.',
                'status' => 'available'
            ],
            [
                'name' => 'Crispy Chicken Wings',
                'price' => 11.00,
                'description' => 'Tender chicken wings tossed in your choice of spicy buffalo or honey garlic sauce.',
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

            // Waiter Permissions
            ['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true],
            ['role' => 'waiter', 'page' => 'kitchen_terminal', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'admin_panel', 'is_allowed' => false],

            // Chef Permissions
            ['role' => 'chef', 'page' => 'waiter_terminal', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true],
            ['role' => 'chef', 'page' => 'admin_panel', 'is_allowed' => false],
        ];

        foreach ($permissions as $perm) {
            RolePermission::updateOrCreate(
                ['role' => $perm['role'], 'page' => $perm['page']],
                ['is_allowed' => $perm['is_allowed']]
            );
        }
    }
}
