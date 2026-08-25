<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialita', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->text('descrizione')->nullable();
            $table->unsignedSmallInteger('durata_visita_minuti')->default(30);
            $table->decimal('costo', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialita');
    }
};
