<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appuntamento_id')->unique()->constrained('appuntamenti')->cascadeOnDelete();
            $table->string('diagnosi');
            $table->text('descrizione');
            $table->text('prescrizione')->nullable();
            $table->date('data_emissione');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referti');
    }
};
