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
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('tax_total')->default(0)->after('total');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->bigInteger('tax_amount')->default(0)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('tax_total');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
        });
    }
};
