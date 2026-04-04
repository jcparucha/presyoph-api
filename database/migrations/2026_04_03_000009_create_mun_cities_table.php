<?php

use App\Models\Province;
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
        Schema::create('mun_cities', function (Blueprint $table) {
            $table->charset("utf8mb4");
            $table->collation("utf8mb4_unicode_ci");

            $table->id();
            $table->foreignIdFor(Province::class)->constrained();
            $table->string("district", length: 10);
            $table->string("code", length: 9);
            $table->string("description", length: 255);

            $table->index("province_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muncities');
    }
};
