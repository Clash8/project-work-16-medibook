<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialitaRequest;
use App\Http\Resources\SpecialitaResource;
use App\Models\Specialita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpecialitaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SpecialitaResource::collection(
            Specialita::orderBy('nome')->get()
        );
    }

    public function show(Specialita $specialita): SpecialitaResource
    {
        return new SpecialitaResource($specialita);
    }

    public function store(SpecialitaRequest $request): JsonResponse
    {
        $specialita = Specialita::create($request->validated());

        return (new SpecialitaResource($specialita))
            ->response()
            ->setStatusCode(201);
    }

    public function update(SpecialitaRequest $request, Specialita $specialita): SpecialitaResource
    {
        $specialita->update($request->validated());

        return new SpecialitaResource($specialita);
    }

    /** Una specialita a cui sono ancora associati dei medici non puo essere eliminata. */
    public function destroy(Specialita $specialita): JsonResponse
    {
        abort_if(
            $specialita->medici()->exists(),
            409,
            'La specialità ha ancora dei medici associati e non può essere eliminata.'
        );

        $specialita->delete();

        return response()->json(['message' => 'Specialità eliminata con successo.']);
    }
}
