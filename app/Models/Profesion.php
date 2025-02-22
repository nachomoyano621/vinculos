<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesion extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'profesiones';
    // Atributos asignables masivamente
    protected $fillable = [
        'nombre',
    ];

    public function profesionales()
    {
        return $this->hasMany(Profesional::class);
    }
}