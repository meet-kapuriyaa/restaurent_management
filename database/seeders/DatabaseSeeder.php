<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Table;
use App\Models\FoodItem;
use App\Models\RolePermission;
use App\Models\Feature;
use App\Models\Module;
use App\Models\Icon;
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

        // Seed Categories
        $categoriesData = [
  0 => 'Idli',
  1 => 'Dosa',
  2 => 'Sides',
  3 => 'Beverages',
  4 => 'Desserts',
  5 => 'Main Course',
];
        $categoriesMap = [];
        foreach ($categoriesData as $catName) {
            $cat = \App\Models\Category::firstOrCreate(['name' => $catName]);
            $categoriesMap[$catName] = $cat->id;
        }

        // Seed Food Items
        $foodItems = [
            [
                'name' => 'Classic Steamed Idli (2 Pcs)',
                'price' => 40.00,
                'description' => 'Soft, fluffy steamed rice cakes served with signature coconut chutney, tomato chutney, and piping hot sambar.',
                'status' => 'available',
                'category_id' => $categoriesMap['Idli'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Ghee Podi Idli',
                'price' => 60.00,
                'description' => 'Spongy idlis tossed in pure cow ghee and aromatic South Indian gunpowder (podi) spice mix.',
                'status' => 'available',
                'category_id' => $categoriesMap['Idli'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Mini Coin Idli',
                'price' => 50.00,
                'description' => 'Bite-sized, pillowy soft button idlis drenched in aromatic sambar and fresh coconut chutney.',
                'status' => 'available',
                'category_id' => $categoriesMap['Idli'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Classic Masala Dosa',
                'price' => 90.00,
                'description' => 'Golden crispy rice crepe stuffed with spiced potato mash, served with coconut chutney and hot sambar.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Mysore Masala Dosa',
                'price' => 100.00,
                'description' => 'Crispy dosa spread with a spicy red garlic chutney lining and seasoned potato filling.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Rava Onion Dosa',
                'price' => 110.00,
                'description' => 'Crispy, lacy semolina crepe loaded with chopped onions, cumin seeds, and fresh green chillies.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Medu Vada (2 Pcs)',
                'price' => 40.00,
                'description' => 'Golden-fried black gram donuts seasoned with peppercorns, ginger, and curry leaves.',
                'status' => 'available',
                'category_id' => $categoriesMap['Sides'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Filter Coffee',
                'price' => 30.00,
                'description' => 'Authentic hot South Indian decoction coffee brewed with milk and served in a traditional brass dabarah cup.',
                'status' => 'available',
                'category_id' => $categoriesMap['Beverages'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Kesari Bath',
                'price' => 40.00,
                'description' => 'Traditional semolina dessert pudding cooked with clarified butter, cardamom, and roasted cashews.',
                'status' => 'available',
                'category_id' => $categoriesMap['Desserts'],
                'image_path' => NULL,
            ],
            [
                'name' => 'Banne Dosa',
                'price' => 80.00,
                'description' => 'Crisp, aromatic Davangere-style butter crepe served with coconut chutney.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => null,
            ],
            [
                'name' => 'Banne Masala Dosa',
                'price' => 100.00,
                'description' => 'Davangere butter crepe filled with seasoned potato mash, served with coconut chutney.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => null,
            ],
            [
                'name' => 'Banne Podi Dosa',
                'price' => 90.00,
                'description' => 'Delicious butter crepe sprinkled with aromatic podi spice gunpowder.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => null,
            ],
            [
                'name' => 'Banne Podi Masala Dosa',
                'price' => 110.00,
                'description' => 'Butter crepe with gunpowder spices and potato filling, served with chutney.',
                'status' => 'available',
                'category_id' => $categoriesMap['Dosa'],
                'image_path' => null,
            ],
            [
                'name' => 'Mini Tiffin',
                'price' => 120.00,
                'description' => 'A traditional South Indian breakfast platter containing mini idli, medu vada, sweet kesari, and a mini dosa.',
                'status' => 'available',
                'category_id' => $categoriesMap['Main Course'],
                'image_path' => null,
            ],
        ];

        foreach ($foodItems as $item) {
            FoodItem::updateOrCreate(['name' => $item['name']], $item);
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
            ['role' => 'admin', 'page' => 'manage_roles', 'is_allowed' => true],

            // Waiter Permissions
            ['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true],
            ['role' => 'waiter', 'page' => 'kitchen_terminal', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'admin_panel', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_insert', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_update', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'can_delete', 'is_allowed' => false],
            ['role' => 'waiter', 'page' => 'manage_roles', 'is_allowed' => false],

            // Chef Permissions
            ['role' => 'chef', 'page' => 'waiter_terminal', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true],
            ['role' => 'chef', 'page' => 'admin_panel', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_insert', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_update', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'can_delete', 'is_allowed' => false],
            ['role' => 'chef', 'page' => 'manage_roles', 'is_allowed' => false],
        ];

        foreach ($permissions as $perm) {
            RolePermission::updateOrCreate(
                ['role' => $perm['role'], 'page' => $perm['page']],
                ['is_allowed' => $perm['is_allowed']]
            );
        }

        // Seed default toggleable features
        $features = [
            [
                'key' => 'waiter_terminal',
                'display_name' => 'Waiter Terminal & Dine-In Ordering',
                'description' => 'Allows waiters to access the table order taking terminal and place dine-in orders.',
                'is_enabled' => true,
            ],
            [
                'key' => 'kitchen_terminal',
                'display_name' => 'Kitchen Display System (KDS)',
                'description' => 'Allows chefs to view active orders in the kitchen and mark them as ready.',
                'is_enabled' => true,
            ],
            [
                'key' => 'upi_checkout',
                'display_name' => 'UPI Quick Scan Checkouts',
                'description' => 'Displays dynamic UPI checkout QR codes on printed receipts and bill pages.',
                'is_enabled' => true,
            ],
            [
                'key' => 'salary_payroll',
                'display_name' => 'Employee Salaries & Payroll',
                'description' => 'Displays and allows managing salary records for employees in staff screens.',
                'is_enabled' => true,
            ],
            [
                'key' => 'audit_reports',
                'display_name' => 'Audit Logs & Sales CSV Downloads',
                'description' => 'Allows viewing the financial audit logs and exporting sales ledger as CSV.',
                'is_enabled' => true,
            ],
            [
                'key' => 'table_selection_required',
                'display_name' => 'Require Table Selection',
                'description' => 'Requires selecting a table for Dine-in orders. If disabled, tables can be omitted, and orders placed without a table selection.',
                'is_enabled' => true,
            ],
        ];

        foreach ($features as $f) {
            Feature::updateOrCreate(['key' => $f['key']], $f);
        }

        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        if ($driver === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF;');
        }

        Module::truncate();

        if ($driver === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON;');
        }

        $modules = [
            [
                'title' => 'Analytics Hub',
                'url' => '#dashboard-pane',
                'parent_id' => null,
                'icon_class' => 'bi-graph-up-arrow',
                'is_visible' => true,
                'order_weight' => 1,
            ],
            [
                'title' => 'Menu Management',
                'url' => '#menu-pane',
                'parent_id' => null,
                'icon_class' => 'bi-egg-fried',
                'is_visible' => true,
                'order_weight' => 2,
            ],
            [
                'title' => 'Orders Log',
                'url' => '#orders-pane',
                'parent_id' => null,
                'icon_class' => 'bi-receipt',
                'is_visible' => true,
                'order_weight' => 3,
            ],
            [
                'title' => 'Employees',
                'url' => '#access-pane',
                'parent_id' => null,
                'icon_class' => 'bi-people',
                'is_visible' => true,
                'order_weight' => 4,
            ],
            [
                'title' => 'Tables Setup',
                'url' => '#tables-pane',
                'parent_id' => null,
                'icon_class' => 'bi-grid-3x3-gap',
                'is_visible' => true,
                'order_weight' => 5,
            ],
            [
                'title' => 'Customer CRM',
                'url' => '#crm-pane',
                'parent_id' => null,
                'icon_class' => 'bi-people-fill',
                'is_visible' => true,
                'order_weight' => 6,
            ],
            [
                'title' => 'Audit Reports',
                'url' => '#audit-pane',
                'parent_id' => null,
                'icon_class' => 'bi-file-earmark-bar-graph',
                'is_visible' => true,
                'order_weight' => 7,
            ],
        ];

        foreach ($modules as $m) {
            Module::updateOrCreate(
                ['title' => $m['title']],
                $m
            );
        }

        // Truncate and seed default icons
        Icon::truncate();
        $icons = [
            ['name' => 'Dashboard Icon', 'class' => 'bi-speedometer2'],
            ['name' => 'Food Icon', 'class' => 'bi-egg-fried'],
            ['name' => 'Category Icon', 'class' => 'bi-tags'],
            ['name' => 'Receipt Icon', 'class' => 'bi-receipt'],
            ['name' => 'Grid Table Icon', 'class' => 'bi-grid-3x3-gap'],
            ['name' => 'Users Icon', 'class' => 'bi-people-fill'],
            ['name' => 'Chart Icon', 'class' => 'bi-file-earmark-bar-graph'],
            ['name' => 'Message Icon', 'class' => 'bi-chat-left-text'],
            ['name' => 'Settings Icon', 'class' => 'bi-gear-fill'],
            ['name' => 'About Info Icon', 'class' => 'bi-info-circle-fill'],
        ];
        foreach ($icons as $ico) {
            Icon::create($ico);
        }
    }
}
