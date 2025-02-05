<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    // Atributos asignables masivamente
    protected $fillable = [
        'nombre',
        'apellido',
        'cuil',
        'dni',
        'direccion',
        'telefono1',
        'telefono2',
        'nrosocial',
        'observaciones',
        'osocial_id',
    ];

    // Relación con OSocial
    public function osocial()
    {
        return $this->belongsTo(OSocial::class, 'osocial_id');
    }
}