<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            ALTER TABLE cosmetic_testimonials 
            ALTER COLUMN rating TYPE numeric(2,1) USING rating::numeric
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cosmetic_testimonials', function (Blueprint $table) {
            $table->string('rating', 20)->nullable()->change();
        });
    }
};
