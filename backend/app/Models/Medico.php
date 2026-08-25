<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Profilo professionale di un medico: collega l'utente alla specialità
 * e definisce la fascia oraria e i giorni in cui riceve.
 */
class Medico extends Model
{
    use HasFactory;

    protected $table = 'medici';

    protected $fillable = [
        'user_id',
        'specialita_id',
        'numero_albo',
        'biografia',
        'ora_inizio',
        'ora_fine',
        'giorni_lavorativi',
    ];

    protected function casts(): array
    {
        return [
            'giorni_lavorativi' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialita(): BelongsTo
    {
        return $this->belongsTo(Specialita::class);
    }

    public function appuntamenti(): HasMany
    {
        return $this->hasMany(Appuntamento::class);
    }

    public function getNomeCompletoAttribute(): string
    {
        return 'Dott. '.$this->user->nome_completo;
    }

    /** Vero se il medico riceve nel giorno indicato (1 = lunedi ... 7 = domenica). */
    public function lavoraIlGiorno(int $giornoIso): bool
    {
        return in_array($giornoIso, $this->giorni_lavorativi ?? [], true);
    }
}
