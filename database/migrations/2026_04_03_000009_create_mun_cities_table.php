<?php

use App\Models\Province;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mun_cities', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->integer('id');
            $table->string('name', length: 255);
            $table->char('code', length: 10);
            $table
                ->foreignIdFor(Province::class, 'province_code')
                ->nullable()
                ->constrained();
            $table->char('district_code', length: 10)->nullable();
            $table->char('city_class', length: 10)->nullable();

            $table->index('province_code');
            $table->primary('code');
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
