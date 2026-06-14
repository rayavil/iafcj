<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $fillable = ['nombre', 'fecha', 'activo'];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Evento $evento) {
            if ($evento->activo) {
                static::where('id', '!=', $evento->id)->update(['activo' => false]);
            }
        });
    }
}
