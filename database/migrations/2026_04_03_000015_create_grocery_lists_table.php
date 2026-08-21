<?php

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
        Schema::create('grocery_lists', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->id();
            $table->foreignIdFor(User::class, 'created_by')->constrained();
            $table->string('name', length: 255);
            $table->string('slug', length: 255)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(0);
            $table->timestamps(precision: 3);
            $table->softDeletes('deleted_at', precision: 3);

            $table->fullText(['name', 'description']);
            // index for `WHERE created_by AND is_public`
            $table->index(['created_by', 'is_public']);
            // index for `WHERE created_by AND is_public ORDER BY created_at`
            $table->index(['created_by', 'is_public', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_lists');
    }
};
