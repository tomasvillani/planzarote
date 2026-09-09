<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'fecha',
        'imagen',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Usuario que ha creado el plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Participantes del plan.
     */
    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    /**
     * Usuarios que participan en el plan.
     */
    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'participantes'
        );
    }
}