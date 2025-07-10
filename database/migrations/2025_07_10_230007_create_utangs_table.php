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
        Schema::create('utangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->constrained()->onDelete('restrict');
            $table->date('jatuh_tempo');
            $table->enum('status_pembayaran', ['belum lunas', 'tercicil', 'sudah lunas'])->default('belum lunas');
            $table->decimal('sudah_dibayar', 15, 0)->default(0);
            $table->decimal('total_harga_modal', 15, 0)->default(0);
            $table->string('foto')->nullable();
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
        Schema::dropIfExists('utangs');
    }
};
