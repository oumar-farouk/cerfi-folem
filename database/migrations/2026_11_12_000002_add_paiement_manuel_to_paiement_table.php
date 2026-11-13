<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Les lignes existantes sont toutes des paiements LigdiCash.
            $table->string('mode', 20)->default('ligdicash')->after('inscription_id'); // ligdicash | especes | virement | cheque | autre
            $table->string('reference_manuelle', 100)->nullable()->after('reference_ligdicash'); // n° de virement / chèque / reçu papier
            $table->text('note')->nullable()->after('devise');
            $table->foreignId('enregistre_par')->nullable()->after('note')->constrained('users')->nullOnDelete();

            $table->index('mode');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enregistre_par');
            $table->dropIndex(['mode']);
            $table->dropColumn(['mode', 'reference_manuelle', 'note']);
        });
    }
};
