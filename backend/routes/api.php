<?php

use App\Http\Controllers\Api\AppuntamentoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DisponibilitaController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\RefertoController;
use App\Http\Controllers\Api\SpecialitaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotte API di MediBook
|--------------------------------------------------------------------------
| Le rotte pubbliche espongono l'autenticazione e il catalogo dell'offerta
| sanitaria; le rotte protette richiedono un token Sanctum valido.
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::get('/specialita', [SpecialitaController::class, 'index']);
Route::get('/specialita/{specialita}', [SpecialitaController::class, 'show']);
Route::get('/medici', [MedicoController::class, 'index']);
Route::get('/medici/{medico}', [MedicoController::class, 'show']);
Route::get('/disponibilita', [DisponibilitaController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/appuntamenti', [AppuntamentoController::class, 'index']);
    Route::get('/appuntamenti/{appuntamento}', [AppuntamentoController::class, 'show']);
    Route::post('/appuntamenti', [AppuntamentoController::class, 'store'])->middleware('ruolo:paziente');
    Route::delete('/appuntamenti/{appuntamento}', [AppuntamentoController::class, 'destroy']);

    Route::get('/referti', [RefertoController::class, 'index']);
    Route::get('/referti/{referto}', [RefertoController::class, 'show']);
    Route::post('/referti', [RefertoController::class, 'store'])->middleware('ruolo:medico');
});
