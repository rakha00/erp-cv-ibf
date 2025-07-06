<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_produk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_produk_id')->constrained()->onDelete('restrict');
            $table->foreignId('unit_produk_id')->constrained()->onDelete('restrict');
            $table->decimal('harga_jual', 15, 0)->default(0);
            $table->integer('jumlah_keluar')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_produk_details');
    }
};
