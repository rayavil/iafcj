<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    protected $fillable = [
        'nombre', 'telefono', 'edad', 'evento_id',
        'padre_espiritual_id', 'padre_espiritual_otro', 'estatus', 'notas', 'ujier_nombre', 'necesidad',
    ];

    public const NECESIDADES = [
        'espirituales' => ['label' => 'Espirituales', 'color' => 'Verde', 'emoji' => '🟢'],
        'milagros' => ['label' => 'Milagros', 'color' => 'Rojo', 'emoji' => '🔴'],
        'fisicas' => ['label' => 'Físicas', 'color' => 'Blanco', 'emoji' => '⚪'],
        'materiales' => ['label' => 'Materiales', 'color' => 'Amarillo', 'emoji' => '🟡'],
        'familiares' => ['label' => 'Familiares', 'color' => 'Naranja', 'emoji' => '🟠'],
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function padreEspiritual(): BelongsTo
    {
        return $this->belongsTo(PadreEspiritual::class, 'padre_espiritual_id');
    }

    public function nombrePadreEspiritual(): string
    {
        return $this->padreEspiritual?->nombre ?? $this->padre_espiritual_otro ?? '—';
    }
}
