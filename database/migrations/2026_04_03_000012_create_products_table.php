<?php

use App\Models\Unit;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->id();
            $table->string('name', length: 100)->fulltext();
            $table->unsignedSmallInteger('weight');
            $table->foreignIdFor(Unit::class)->constrained();
            $table->foreignIdFor(Brand::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->foreignIdFor(User::class, 'added_by')->constrained();
            $table->timestamps(precision: 3);
            $table->softdeletes('deleted_at', precision: 3);

            $table->index(['unit_id', 'brand_id', 'category_id', 'added_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
