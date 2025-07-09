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
        Schema::create('penghasilan_karyawan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained()->onDelete('restrict');
            $table->decimal('bonus_target', 15, 0)->default(0);
            $table->decimal('uang_makan', 15, 0)->default(0);
            $table->decimal('tunjangan_transportasi', 15, 0)->default(0);
            $table->decimal('thr', 15, 0)->default(0);
            $table->decimal('keterlambatan', 15, 0)->default(0);
            $table->decimal('tanpa_keterangan', 15, 0)->default(0);
            $table->decimal('pinjaman', 15, 0)->default(0);
            $table->date('tanggal');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghasilan_karyawan_details');
    }
};
