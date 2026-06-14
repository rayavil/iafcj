<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PadreEspiritual extends Model
{
    protected $table = 'padres_espirituales';

    protected $fillable = ['nombre', 'telefono'];

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }
}
