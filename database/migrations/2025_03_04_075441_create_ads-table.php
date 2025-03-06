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
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id_ad');
            $table->unsignedBigInteger('id_utilisateur');
            $table->unsignedBigInteger('id_category');
            $table->string('titre', 1000)->notNullable();
            $table->text('description')->notNullable();
            $table->decimal('prix', 10, 2); // Prix de l'annonce
            $table->string('emplacement', 100); // Localisation (ville)
            $table->enum('statut', ['en attente', 'actif', 'expiré', 'supprimé'])->default('en attente'); // Statut de l'annonce
            $table->timestamps();

            //Contraintes des clés etrangères 
            $table->foreign('id_utilisateur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_category')->references('id_category')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('ads');
    }
};
