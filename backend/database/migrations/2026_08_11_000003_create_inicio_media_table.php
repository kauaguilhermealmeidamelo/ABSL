<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inicio_media', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('url');
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inicio_media');
    }
};
