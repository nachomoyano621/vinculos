<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesionalesTable extends Migration
{
    public function up()
    {
        Schema::create('profesionales', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cuil')->nullable();
            $table->string('dni')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono1')->nullable();
            $table->string('telefono2')->nullable();
            $table->text('observaciones')->nullable();

            // Relación con la tabla 'profesiones'
            $table->foreignId('profesion_id')->constrained('profesiones')->onDelete('cascade'); 

            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // Añadimos el soporte para Soft Deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('profesionales');
    }
}

