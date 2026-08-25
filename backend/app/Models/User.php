<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Utente del sistema. Il campo "ruolo" realizza la generalizzazione
 * Paziente / Medico / Amministratore prevista dal modello concettuale.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const RUOLO_PAZIENTE = 'paziente';
    public const RUOLO_MEDICO = 'medico';
    public const RUOLO_ADMIN = 'admin';

    protected $fillable = [
        'nome',
        'cognome',
        'email',
        'password',
        'ruolo',
        'telefono',
        'codice_fiscale',
        'data_nascita',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'data_nascita' => 'date',
            'password' => 'hashed',
        ];
    }

    /** Profilo professionale, presente solo per gli utenti con ruolo "medico". */
    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class);
    }

    /** Appuntamenti prenotati dall'utente in qualità di paziente. */
    public function appuntamenti(): HasMany
    {
        return $this->hasMany(Appuntamento::class, 'paziente_id');
    }

    public function getNomeCompletoAttribute(): string
    {
        return trim("{$this->nome} {$this->cognome}");
    }

    public function isPaziente(): bool
    {
        return $this->ruolo === self::RUOLO_PAZIENTE;
    }

    public function isMedico(): bool
    {
        return $this->ruolo === self::RUOLO_MEDICO;
    }

    public function isAdmin(): bool
    {
        return $this->ruolo === self::RUOLO_ADMIN;
    }
}
