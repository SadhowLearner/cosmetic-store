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
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->decimal('sub_total_amount', 15, 2)->unsigned()->change();
            $table->decimal('total_amount', 15, 2)->unsigned()->change();
            $table->decimal('total_tax_amount', 15, 2)->unsigned()->change();
            $table->renameColumn('qty', 'total_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->bigInteger('sub_total_amount')->unsigned()->change();
            $table->bigInteger('total_amount')->unsigned()->change();
            $table->bigInteger('total_tax_amount')->unsigned()->change();
            $table->renameColumn('total_qty', 'qty');
        });
    }
};
