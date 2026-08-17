<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // Panel, Atelier, Cérémonie, Networking...
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin')->nullable();
            $table->string('salle')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();

            $table->index(['edition_id', 'date']);
        });

        Schema::create('intervenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('fonction')->nullable();
            $table->string('structure')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();
        });

        Schema::create('intervenant_programme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intervenant_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['programme_id', 'intervenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intervenant_programme');
        Schema::dropIfExists('intervenants');
        Schema::dropIfExists('programmes');
    }
};
