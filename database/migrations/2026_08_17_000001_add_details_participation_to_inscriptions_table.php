<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs complémentaires collectés au formulaire d'inscription :
     *  - les jours de présence effectivement choisis (utile pour la logistique
     *    restauration et le dimensionnement des salles) ;
     *  - les besoins particuliers, alimentaires ou d'accessibilité ;
     *  - le canal par lequel le participant a connu le forum, pour la
     *    commission communication.
     */
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->json('jours_participation')->nullable()->after('profil_id');
            $table->text('besoins_particuliers')->nullable()->after('jours_participation');
            $table->string('source_connaissance', 100)->nullable()->after('besoins_particuliers');

            $table->index('source_connaissance');
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropIndex(['source_connaissance']);
            $table->dropColumn(['jours_participation', 'besoins_particuliers', 'source_connaissance']);
        });
    }
};
