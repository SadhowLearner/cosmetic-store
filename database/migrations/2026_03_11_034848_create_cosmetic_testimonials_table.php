<?php

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
        Schema::create('cosmetic_testimonials', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->text('message')->nullable();
            $table->string('rating', 100)->nullable();
            $table->string('photo')->nullable();

            $table->foreignId('cosmetic_id')->constrained()->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosmetic_testimonials');
    }
};
