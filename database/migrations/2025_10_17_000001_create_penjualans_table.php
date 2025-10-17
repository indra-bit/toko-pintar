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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('penjualan_id')->nullable()->after('id')->constrained('penjualans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('penjualan_id');
        });

        Schema::dropIfExists('penjualans');
    }
};
