<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefertoRequest;
use App\Http\Resources\RefertoResource;
use App\Models\Appuntamento;
use App\Models\Referto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RefertoController extends Controller
{
    /** Referti visibili all'utente: i propri per il paziente, quelli emessi per il medico. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $utente = $request->user();

        $query = Referto::query()
            ->with(['appuntamento.medico.user', 'appuntamento.medico.specialita', 'appuntamento.paziente'])
            ->whereHas('appuntamento', function ($q) use ($utente) {
                if ($utente->isPaziente()) {
                    $q->where('paziente_id', $utente->id);
                } elseif ($utente->isMedico()) {
                    $q->where('medico_id', $utente->medico->id);
                }
            })
            ->orderByDesc('data_emissione');

        return RefertoResource::collection($query->paginate(10));
    }

    public function show(Request $request, Referto $referto): RefertoResource
    {
        $this->authorize('view', $referto);

        return new RefertoResource(
            $referto->load(['appuntamento.medico.user', 'appuntamento.medico.specialita', 'appuntamento.paziente'])
        );
    }

    /** Emissione del referto: consentita al solo medico che ha effettuato la visita. */
    public function store(StoreRefertoRequest $request): JsonResponse
    {
        $appuntamento = Appuntamento::findOrFail($request->integer('appuntamento_id'));

        $this->authorize('create', [Referto::class, $appuntamento]);

        $referto = Referto::create([
            ...$request->validated(),
            'data_emissione' => now()->toDateString(),
        ]);

        $appuntamento->update(['stato' => Appuntamento::STATO_COMPLETATO]);

        return (new RefertoResource($referto->load('appuntamento.medico.user', 'appuntamento.medico.specialita')))
            ->response()
            ->setStatusCode(201);
    }
}
