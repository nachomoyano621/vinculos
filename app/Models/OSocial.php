<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OSocial extends Model
{
    use HasFactory, SoftDeletes;

    // Especifica el nombre de la tabla
    protected $table = 'vinculos.osocial';

    // Atributos asignables
    protected $fillable = [
        'nombre',
    ];

    // Fechas mutables (incluye deleted_at)
    protected $dates = [
        'deleted_at',
    ];
}