<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Specialita medica erogata dalla clinica (es. Cardiologia, Dermatologia). */
class Specialita extends Model
{
    use HasFactory;

    protected $table = 'specialita';

    protected $fillable = [
        'nome',
        'descrizione',
        'durata_visita_minuti',
        'costo',
    ];

    protected function casts(): array
    {
        return [
            'durata_visita_minuti' => 'integer',
            'costo' => 'decimal:2',
        ];
    }

    public function medici(): HasMany
    {
        return $this->hasMany(Medico::class);
    }
}
