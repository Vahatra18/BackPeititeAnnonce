<?php

use Google\Service\Dfareporting\Resource\Ads;
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
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id_image');
            $table->unsignedBigInteger('id_ad');
            $table->string('path');
            $table->boolean('est_principal')->notNullable()->default(0);
            $table->timestamps();

            //Foreign key constraint
            $table->foreign('id_ad')->references('id_ad')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('images');
    }
};
