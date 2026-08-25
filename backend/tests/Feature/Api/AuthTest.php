<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visitatore_puo_registrarsi_come_paziente(): void
    {
        $risposta = $this->postJson('/api/register', [
            'nome' => 'Marco',
            'cognome' => 'Rossi',
            'email' => 'marco.rossi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $risposta->assertCreated()
            ->assertJsonPath('utente.ruolo', User::RUOLO_PAZIENTE)
            ->assertJsonStructure(['utente' => ['id', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'marco.rossi@example.test']);
    }

    public function test_la_registrazione_rifiuta_dati_non_validi(): void
    {
        $this->postJson('/api/register', [
            'nome' => '',
            'email' => 'non-una-email',
            'password' => '123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'email', 'password']);
    }

    public function test_il_login_restituisce_un_token(): void
    {
        User::factory()->create(['email' => 'paziente@example.test']);

        $this->postJson('/api/login', [
            'email' => 'paziente@example.test',
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['utente', 'token']);
    }

    public function test_il_login_con_credenziali_errate_fallisce(): void
    {
        User::factory()->create(['email' => 'paziente@example.test']);

        $this->postJson('/api/login', [
            'email' => 'paziente@example.test',
            'password' => 'password-sbagliata',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_le_rotte_protette_richiedono_autenticazione(): void
    {
        $this->getJson('/api/appuntamenti')->assertUnauthorized();
    }

    public function test_il_logout_revoca_il_token_corrente(): void
    {
        $utente = User::factory()->create();

        $this->actingAs($utente, 'sanctum')
            ->postJson('/api/logout')
            ->assertOk();
    }
}
