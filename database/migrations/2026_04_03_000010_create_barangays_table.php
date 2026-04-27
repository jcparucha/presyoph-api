<?php

use App\Models\MunCity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->integer('id');
            $table->string('name', length: 255);
            $table->char('code', length: 10);
            $table
                ->foreignIdFor(MunCity::class, 'mun_city_code')
                ->constrained();
            $table->char('province_code', length: 10)->nullable();
            $table->char('region_code', length: 10)->nullable();

            $table->index('mun_city_code');
            $table->primary('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
