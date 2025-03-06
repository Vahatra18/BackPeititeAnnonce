<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favoris', function (Blueprint $table) {
            $table->bigIncrements('id_favoris'); // INT, PRIMARY KEY, AUTO_INCREMENT
            $table->unsignedBigInteger('id_utilisateur')->notNullable(); // Référence à users.id, NOT NULL
            $table->unsignedBigInteger('id_ad')->notNullable(); // Référence à ads.id_ad, NOT NULL
            $table->timestamp('ajoute_a')->useCurrent(); // DATEHEURE, NOT NULL, ACTUEL PAR DÉFAUT
            $table->timestamps();

            // Contraintes de clés étrangères
            $table->foreign('id_utilisateur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_ad')->references('id_ad')->on('ads')->onDelete('cascade');

            // Contrainte unique pour éviter les doublons (un utilisateur ne peut pas favori la même annonce plusieurs fois)
            $table->unique(['id_utilisateur', 'id_ad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoris');
    }
};
