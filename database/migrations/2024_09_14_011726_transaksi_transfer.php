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
        Schema::create('transaksi_transfer', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi')->unique();
            $table->integer('kode_unik');
            $table->integer('nilai_transfer');
            $table->string('bank_tujuan');
            $table->string('rekening_tujuan');
            $table->string('bank_pengirim');
            $table->string('rekening_pengirim');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
