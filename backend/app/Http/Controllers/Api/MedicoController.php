<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicoResource;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MedicoController extends Controller
{
    /** Elenco dei medici, filtrabile per specialità tramite query string. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'specialita' => ['nullable', 'integer', 'exists:specialita,id'],
        ]);

        $medici = Medico::query()
            ->with(['user', 'specialita'])
            ->when($request->filled('specialita'), fn ($query) => $query->where('specialita_id', $request->integer('specialita')))
            ->get()
            ->sortBy('nome_completo')
            ->values();

        return MedicoResource::collection($medici);
    }

    public function show(Medico $medico): MedicoResource
    {
        return new MedicoResource($medico->load(['user', 'specialita']));
    }
}
