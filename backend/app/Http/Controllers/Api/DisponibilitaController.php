<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Services\DisponibilitaService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisponibilitaController extends Controller
{
    public function __construct(private readonly DisponibilitaService $disponibilita) {}

    /** Slot prenotabili di un medico in una data: GET /api/disponibilita?medico=1&data=2026-09-01 */
    public function index(Request $request): JsonResponse
    {
        $dati = $request->validate([
            'medico' => ['required', 'integer', 'exists:medici,id'],
            'data' => ['required', 'date_format:Y-m-d'],
        ]);

        $medico = Medico::with('specialita')->findOrFail($dati['medico']);
        $data = CarbonImmutable::createFromFormat('Y-m-d', $dati['data'])->startOfDay();

        return response()->json([
            'data' => [
                'medico_id' => $medico->id,
                'data' => $data->toDateString(),
                'durata_visita_minuti' => $medico->specialita->durata_visita_minuti,
                'slot' => $this->disponibilita->slotLiberi($medico, $data),
            ],
        ]);
    }
}
