<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medici', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('specialita_id')->constrained('specialita')->restrictOnDelete();
            $table->string('numero_albo', 30)->unique();
            $table->text('biografia')->nullable();
            $table->time('ora_inizio')->default('09:00:00');
            $table->time('ora_fine')->default('17:00:00');
            // Giorni lavorativi ISO-8601: 1 = lunedi ... 7 = domenica
            $table->json('giorni_lavorativi');
            $table->timestamps();

            $table->index('specialita_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medici');
    }
};
