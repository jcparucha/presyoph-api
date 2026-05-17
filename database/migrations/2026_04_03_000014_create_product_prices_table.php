<?php

use App\Models\Establishment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->id();
            $table->foreignIdFor(User::class, 'added_by')->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Establishment::class)->constrained();
            $table->decimal('price');
            $table->timestamps(precision: 3);
            $table->softdeletes('deleted_at', precision: 3);

            $table->index(['added_by', 'product_id', 'establishment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
