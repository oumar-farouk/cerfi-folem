<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();

            // Un pointage par inscription ET par jour : le forum dure plusieurs jours.
            $table->date('jour');
            $table->timestamp('pointe_a');
            $table->string('methode', 10)->default('scan'); // scan | manuel
            $table->foreignId('pointe_par')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Verrou anti-doublon, y compris si deux agents scannent en même temps.
            $table->unique(['inscription_id', 'jour']);
            $table->index('jour');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
