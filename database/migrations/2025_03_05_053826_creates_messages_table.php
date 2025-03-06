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
        //
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id_message');
            $table->unsignedBigInteger('id_send');
            $table->unsignedBigInteger('id_rec');
            $table->unsignedBigInteger('id_ad');
            $table->text('contenu')->notNullable();
            $table->timestamp('dateEnvoi')->useCurrent();
            $table->boolean('est_lu')->notNullable()->default(0); //1 if read 0 else if 

            //constraint foreign key
            $table->foreign('id_send')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_rec')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_ad')->references('id_ad')->on('ads')->onDelete('cascade');

            // Index pour améliorer les performances (optionnel)
            $table->index(['id_send', 'id_rec', 'id_ad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('messages');
    }
};
