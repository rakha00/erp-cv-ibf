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
        Schema::table('transaksi_produk_details', function (Blueprint $table) {
            $table->string('nama_unit')->after('unit_produk_id');
            $table->decimal('harga_modal', 15, 0)->after('harga_jual');
            $table->decimal('total_keuntungan', 15, 0)->after('jumlah_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_produk_details', function (Blueprint $table) {
            $table->dropColumn(['nama_unit', 'harga_modal', 'total_keuntungan']);
        });
    }
};
