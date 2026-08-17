<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();

            // Code unique remis au participant juste après le formulaire (façon FOLEM)
            // Sert à retrouver son inscription pour payer / télécharger le récépissé plus tard.
            $table->string('code_inscription', 20)->unique();

            $table->enum('statut', ['pending', 'paid', 'cancelled', 'expired'])->default('pending');
            $table->unsignedInteger('montant')->default(0);

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['edition_id', 'participant_id']); // pas de double inscription à la même édition
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
