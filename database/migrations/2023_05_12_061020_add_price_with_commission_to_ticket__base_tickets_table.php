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
        Schema::table('ticket__base_tickets', function (Blueprint $table) {
            $table->string("price_with_commission")->after('price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket__base_tickets', function (Blueprint $table) {
            $table->dropColumn('price_with_commission');
        });
    }
};
