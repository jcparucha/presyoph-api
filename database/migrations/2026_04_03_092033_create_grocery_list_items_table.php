<?php

use App\Models\GroceryList;
use App\Models\ProductPrice;
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
        Schema::create('grocery_list_items', function (Blueprint $table) {
            $table->charset("utf8mb4");
            $table->collation("utf8mb4_unicode_ci");

            $table->id();
            $table->foreignIdFor(GroceryList::class);
            $table->foreignIdFor(ProductPrice::class);
            $table->boolean("is_done")->default(0);
            $table->unsignedTinyInteger("quantity");
            $table->decimal("price");
            $table->decimal("subtotal")->storedAs("quantity * price");
            $table->timestamps(precision: 3);
            $table->softdeletes("deleted_at", precision: 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_list_items');
    }
};
