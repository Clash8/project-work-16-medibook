<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppuntamentoRequest;
use App\Http\Resources\AppuntamentoResource;
use App\Models\Appuntamento;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AppuntamentoController extends Controller
{
    /**
     * Agenda dell'utente autenticato: i pazienti vedono le proprie prenotazioni,
     * i medici l'elenco delle visite loro assegnate. Risultato paginato.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $utente = $request->user();

        $query = Appuntamento::query()
            ->with(['medico.user', 'medico.specialita', 'paziente', 'referto'])
            ->when($utente->isPaziente(), fn ($q) => $q->where('paziente_id', $utente->id))
            ->when($utente->isMedico(), fn ($q) => $q->where('medico_id', $utente->medico->id))
            ->when($request->filled('stato'), fn ($q) => $q->where('stato', $request->string('stato')))
            ->orderByDesc('data_ora');

        return AppuntamentoResource::collection($query->paginate(10));
    }

    public function show(Request $request, Appuntamento $appuntamento): AppuntamentoResource
    {
        $this->authorize('view', $appuntamento);

        return new AppuntamentoResource(
            $appuntamento->load(['medico.user', 'medico.specialita', 'paziente', 'referto'])
        );
    }

    /**
     * Crea la prenotazione. La verifica di disponibilità e l'inserimento avvengono
     * nella stessa transazione, con lock pessimistico sulle righe del medico: due
     * richieste concorrenti sullo stesso slot non possono quindi andare entrambe a buon fine.
     */
    public function store(StoreAppuntamentoRequest $request): JsonResponse
    {
        $dataOra = CarbonImmutable::createFromFormat('Y-m-d H:i', $request->input('data_ora'));

        $appuntamento = DB::transaction(function () use ($request, $dataOra) {
            $slotOccupato = Appuntamento::query()
                ->where('medico_id', $request->integer('medico_id'))
                ->where('data_ora', $dataOra->format('Y-m-d H:i:s'))
                ->where('stato', '!=', Appuntamento::STATO_ANNULLATO)
                ->lockForUpdate()
                ->exists();

            abort_if($slotOccupato, 409, 'Lo slot selezionato non è più disponibile.');

            return Appuntamento::create([
                ...$request->validated(),
                'data_ora' => $dataOra,
                'paziente_id' => $request->user()->id,
                'stato' => Appuntamento::STATO_PRENOTATO,
            ]);
        });

        $appuntamento->load(['medico.user', 'medico.specialita', 'paziente']);

        return (new AppuntamentoResource($appuntamento))
            ->response()
            ->setStatusCode(201);
    }

    /** Annullamento logico: lo storico della prenotazione viene conservato. */
    public function destroy(Request $request, Appuntamento $appuntamento): JsonResponse
    {
        $this->authorize('delete', $appuntamento);

        abort_unless(
            $appuntamento->isAnnullabile(),
            422,
            'La visita non può essere annullata perché già svolta o già annullata.'
        );

        $appuntamento->update(['stato' => Appuntamento::STATO_ANNULLATO]);

        return response()->json(['message' => 'Prenotazione annullata con successo.']);
    }
}
