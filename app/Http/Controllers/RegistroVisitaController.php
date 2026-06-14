<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\PadreEspiritual;
use App\Models\Ujier;
use App\Models\Visita;
use Illuminate\Http\Request;

class RegistroVisitaController extends Controller
{
    public function create()
    {
        $evento = Evento::where('activo', true)->latest('fecha')->first();
        $padres = PadreEspiritual::orderBy('nombre')->get();
        $ujieres = Ujier::orderBy('nombre')->get();
        $visitasCount = $evento ? $evento->visitas()->count() : 0;

        return view('registro', [
            'evento' => $evento,
            'padres' => $padres,
            'ujieres' => $ujieres,
            'visitasCount' => $visitasCount,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'digits:10'],
            'padre_espiritual_id' => ['nullable'],
            'padre_espiritual_otro' => ['nullable', 'string', 'max:150'],
            'notas' => ['nullable', 'string'],
            'ujier_nombre' => ['nullable', 'string', 'max:150'],
            'necesidad' => ['nullable', 'in:' . implode(',', array_keys(\App\Models\Visita::NECESIDADES))],
        ], [
            'nombre.required' => 'Cuéntanos cómo se llama la visita.',
            'nombre.max' => 'Ese nombre es demasiado largo (máximo 150 caracteres).',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
        ]);

        if (($data['padre_espiritual_id'] ?? null) === 'otro' || empty($data['padre_espiritual_id'])) {
            $data['padre_espiritual_id'] = null;
        } else {
            $data['padre_espiritual_id'] = (int) $data['padre_espiritual_id'];
        }

        $evento = Evento::where('activo', true)->latest('fecha')->first();

        $duplicada = null;
        if (!empty($data['telefono'])) {
            $duplicada = Visita::where('evento_id', $evento?->id)
                ->whereRaw('LOWER(nombre) = ?', [mb_strtolower(trim($data['nombre']))])
                ->where('telefono', $data['telefono'])
                ->first();
        }

        if ($duplicada) {
            $duplicada->fill($data)->save();
            $esDuplicado = true;
            $mensaje = 'Esta persona ya estaba registrada; actualizamos su información.';
        } else {
            Visita::create([
                ...$data,
                'evento_id' => $evento?->id,
                'estatus' => 'nuevo',
            ]);
            $esDuplicado = false;
            $mensaje = '¡Gracias! El registro se guardó correctamente.';
        }

        $visitasCount = $evento ? $evento->visitas()->count() : 0;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'duplicado' => $esDuplicado,
                'message' => $mensaje,
                'visitasCount' => $visitasCount,
            ]);
        }

        return redirect()->route('registro')
            ->with('success', $mensaje)
            ->with('duplicado', $esDuplicado)
            ->with('visitasCount', $visitasCount);
    }
}
