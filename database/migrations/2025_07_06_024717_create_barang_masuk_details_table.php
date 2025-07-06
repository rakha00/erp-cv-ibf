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
        Schema::create('barang_masuk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->constrained()->onDelete('restrict');
            $table->foreignId('unit_produk_id')->constrained()->onDelete('restrict');
            $table->string('sku');
            $table->string('nama_unit');
            $table->decimal('harga_modal', 20, 0);
            $table->integer('jumlah_barang_masuk');
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
        Schema::dropIfExists('barang_masuk_details');
    }
};
