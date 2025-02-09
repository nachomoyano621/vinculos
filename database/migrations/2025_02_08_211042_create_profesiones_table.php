<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesionesTable extends Migration
{
    public function up()
    {
        Schema::create('profesiones', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('nombre', 1000); // Campo de texto con máximo 1000 caracteres
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para Soft Deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('profesiones');
    }
}