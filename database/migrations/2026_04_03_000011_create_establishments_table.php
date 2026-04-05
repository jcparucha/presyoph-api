<?php

use App\Models\Barangay;
use App\Models\StoreType;
use App\Models\User;
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
        Schema::create('establishments', function (Blueprint $table) {
            $table->charset("utf8mb4");
            $table->collation("utf8mb4_unicode_ci");

            $table->id();
            $table->string("name", length: 100)->fulltext();
            $table->foreignIdFor(Barangay::class)->constrained();
            $table->foreignIdFor(StoreType::class)->constrained();
            $table->foreignIdFor(User::class, "added_by")->constrained();
            $table->timestamps(precision: 3);
            $table->softdeletes('deleted_at', precision: 3);

            $table->index(["barangay_id", "store_type_id", "added_by"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
