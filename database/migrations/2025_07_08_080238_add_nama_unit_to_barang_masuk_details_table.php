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
        Schema::table('barang_masuk_details', function (Blueprint $table) {
            $table->string('nama_unit')->after('unit_produk_id');
            $table->decimal('total_harga_modal', 15, 0)->after('jumlah_barang_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk_details', function (Blueprint $table) {
            $table->dropColumn('nama_unit');
            $table->dropColumn('total_harga_modal');
        });
    }
};
