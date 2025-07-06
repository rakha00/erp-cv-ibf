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
        Schema::create('pengahasilan_karyawan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained()->onDelete('restrict');
            $table->decimal('kasbon', 15, 0)->default(0);
            $table->decimal('lembur', 15, 0)->default(0);
            $table->decimal('bonus', 15, 0)->default(0);
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
        Schema::dropIfExists('pengahasilan_karyawan_details');
    }
};
