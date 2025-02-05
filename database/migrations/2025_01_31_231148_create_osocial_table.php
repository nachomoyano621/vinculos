<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('osocial', function (Blueprint $table) {
            $table->id(); // Campo ID autoincremental
            $table->string('nombre')->unique(); // Nombre de la obra social (único)
            $table->softDeletes(); // Agrega la columna deleted_at para Soft Deletes
            $table->timestamps(); // Agrega las columnas created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('osocial');
    }
};