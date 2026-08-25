<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialitaResource;
use App\Models\Specialita;
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
}
