<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();

            $table->string('operateur')->nullable();       // Orange Money / Moov Money / Telecel (renvoyé par LigdiCash)
            $table->string('token_transaction')->nullable()->index(); // token LigdiCash (invoiceToken)
            $table->string('reference_ligdicash')->nullable();

            $table->enum('statut', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->unsignedInteger('montant');
            $table->string('devise', 3)->default('XOF');

            $table->json('payload_creation')->nullable();   // réponse de checkout-invoice/create
            $table->json('payload_callback')->nullable();   // corps brut reçu sur le webhook callback_url
            $table->json('payload_confirmation')->nullable();// réponse de checkout-invoice/confirm

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
