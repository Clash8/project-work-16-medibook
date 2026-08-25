<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

/** Registrazione, autenticazione e revoca del token (Laravel Sanctum). */
class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $utente = User::create([
            ...$request->validated(),
            'ruolo' => User::RUOLO_PAZIENTE,
        ]);

        return response()->json([
            'utente' => new UserResource($utente),
            'token' => $utente->createToken('medibook')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $utente = User::where('email', $request->input('email'))->first();

        if (! $utente || ! Hash::check($request->input('password'), $utente->password)) {
            throw ValidationException::withMessages([
                'email' => 'Le credenziali fornite non sono corrette.',
            ]);
        }

        $utente->loadMissing('medico');

        return response()->json([
            'utente' => new UserResource($utente),
            'token' => $utente->createToken('medibook')->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        // In presenza di un token personale Sanctum la revoca è effettiva; con il
        // token di sessione usato nei test funzionali l'operazione è un no-op.
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Logout effettuato con successo.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->loadMissing('medico'));
    }
}
