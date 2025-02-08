<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasTable extends Migration
{
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade'); // Relación con pacientes
            $table->text('nombre')->nullable(false)->comment('Texto de la nota, máximo 1000 caracteres');
            $table->string('usuario_registro')->nullable(false)->comment('Usuario que registró la nota');
            $table->timestamps(); // Columnas created_at y updated_at
            $table->softDeletes(); // Columna deleted_at para Soft Deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('notas');
    }
}