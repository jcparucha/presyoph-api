<?php

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
        Schema::create('categories', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->id();
            $table->string('name', length: 100)->fulltext();
            $table->string('description', length: 255)->nullable();
            $table->foreignIdFor(User::class, 'added_by')->nullable();
            $table->timestamps(precision: 3);
            $table->softdeletes('deleted_at', precision: 3);

            $table->index(['added_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
