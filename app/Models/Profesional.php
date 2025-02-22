<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesional extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profesionales';
    protected $fillable = [
        'nombre',
        'apellido',
        'cuil',
        'dni',
        'direccion',
        'telefono1',
        'telefono2',
        'observaciones',
        'profesion_id',
    ];

    // Relación con la tabla Profesion
    public function profesion()
    {
        return $this->belongsTo(Profesion::class);
    }
}