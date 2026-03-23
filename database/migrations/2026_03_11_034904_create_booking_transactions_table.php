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
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('booking_trx_id');
            $table->string('name');
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('proof', 100)->nullable();
            $table->string('post_code', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('address');

            $table->unsignedBigInteger('qty')->default(0);
            $table->bigInteger('sub_total_amount')->unsigned();
            $table->bigInteger('total_amount')->unsigned();
            $table->bigInteger('total_tax_amount')->unsigned();
            
            $table->boolean('is_paid')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};
