<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event', 150);
            $table->text('deskripsi')->nullable();
            $table->dateTime('jadwal');
            $table->string('lokasi', 150)->nullable();
            $table->integer('kuota')->nullable();
            $table->enum('jenis', ['gratis', 'berbayar']);
            $table->boolean('is_sertifikat')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};