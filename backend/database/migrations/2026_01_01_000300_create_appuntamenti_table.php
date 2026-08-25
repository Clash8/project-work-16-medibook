<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appuntamenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paziente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medici')->cascadeOnDelete();
            $table->dateTime('data_ora');
            $table->enum('stato', ['prenotato', 'completato', 'annullato'])->default('prenotato');
            $table->string('motivo')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // Un medico non può avere due appuntamenti attivi sullo stesso slot:
            // il vincolo e garantito a livello di database, oltre che applicativo.
            $table->unique(['medico_id', 'data_ora'], 'appuntamenti_slot_unique');
            $table->index(['paziente_id', 'data_ora']);
            $table->index('stato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appuntamenti');
    }
};
