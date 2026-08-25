<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Esito clinico di una visita effettuata, redatto dal medico. */
class Referto extends Model
{
    use HasFactory;

    protected $table = 'referti';

    protected $fillable = [
        'appuntamento_id',
        'diagnosi',
        'descrizione',
        'prescrizione',
        'data_emissione',
    ];

    protected function casts(): array
    {
        return [
            'data_emissione' => 'date',
        ];
    }

    public function appuntamento(): BelongsTo
    {
        return $this->belongsTo(Appuntamento::class);
    }
}
