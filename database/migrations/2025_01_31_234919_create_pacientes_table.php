<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacientesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pacientes')) {
            Schema::create('pacientes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('apellido');
                $table->string('cuil')->nullable();
                $table->string('dni')->nullable();
                $table->string('direccion')->nullable();
                $table->string('telefono1')->nullable();
                $table->string('telefono2')->nullable();
                $table->string('nrosocial')->nullable();
                $table->text('observaciones')->nullable();

                // Clave foránea corregida
                $table->foreignId('osocial_id')->constrained('osocial')->onDelete('cascade');
                $table->softDeletes(); // Columna deleted_at para Soft Deletes
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
}
