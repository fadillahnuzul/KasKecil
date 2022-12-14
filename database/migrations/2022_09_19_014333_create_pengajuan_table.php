<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->required();
            $table->string('deskripsi', 100)->required();
            $table->string('status')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('sumber')->nullable();
            $table->string('pengeluaran')->nullable();
            $table->integer('riwayat_saldo')->nullable();
            $table->string('divisi_id');
            $table->string('kode')->nullable();
            $table->string('divisi')->nullable();
            $table->integer('tunai')->nullable();
            $table->integer('bank')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan');
    }
};
