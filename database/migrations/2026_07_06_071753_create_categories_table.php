<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Extract existing categories from food_items table and seed them
        if (Schema::hasColumn('food_items', 'category')) {
            $existingCategories = DB::table('food_items')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');

            foreach ($existingCategories as $catName) {
                if (!empty(trim($catName))) {
                    DB::table('categories')->insertOrIgnore([
                        'name' => trim($catName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Add category_id to food_items table (nullable, indexed)
        Schema::table('food_items', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->index()->after('status');
        });

        // 4. Map existing items' category strings to their matching category_id
        if (Schema::hasColumn('food_items', 'category')) {
            $categories = DB::table('categories')->get()->keyBy('name');
            $foodItems = DB::table('food_items')->get();

            foreach ($foodItems as $item) {
                $catName = trim($item->category ?? '');
                $catId = isset($categories[$catName]) ? $categories[$catName]->id : null;
                if ($catId) {
                    DB::table('food_items')
                        ->where('id', $item->id)
                        ->update(['category_id' => $catId]);
                }
            }
            
            // 5. Drop the old category column from food_items
            Schema::table('food_items', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the category column
        Schema::table('food_items', function (Blueprint $table) {
            $table->string('category')->default('Uncategorized')->after('status');
        });

        // Map category names back to the column
        if (Schema::hasTable('categories')) {
            $foodItems = DB::table('food_items')
                ->join('categories', 'food_items.category_id', '=', 'categories.id')
                ->select('food_items.id', 'categories.name as cat_name')
                ->get();

            foreach ($foodItems as $item) {
                DB::table('food_items')
                    ->where('id', $item->id)
                    ->update(['category' => $item->cat_name]);
            }
        }

        // Drop category_id and its foreign key constraint
        Schema::table('food_items', function (Blueprint $table) {
            // Drop foreign key safely
            try {
                $table->dropForeign(['category_id']);
            } catch (\Exception $e) {
                // Ignore if not supported or not present
            }
            $table->dropColumn('category_id');
        });

        // Drop categories table
        Schema::dropIfExists('categories');
    }
};
