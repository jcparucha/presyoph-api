<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TODO: remove this later
     * Determine if this migration should run.
     */
    // public function shouldRun(): bool
    // {
    //     return false;
    // }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->charset("utf8mb4");
            $table->collation("utf8mb4_unicode_ci");

            $table->id();
            $table->string("username", length: 50);
            $table->string("password", length: 255);
            $table->boolean("is_test_account")->default(0);
            $table->timestamps(precision: 3);
            $table->softdeletes('deleted_at', precision: 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
