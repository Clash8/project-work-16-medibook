<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Prenotazione di una visita: associa un paziente a un medico su uno slot orario. */
class Appuntamento extends Model
{
    use HasFactory;

    public const STATO_PRENOTATO = 'prenotato';
    public const STATO_COMPLETATO = 'completato';
    public const STATO_ANNULLATO = 'annullato';

    protected $table = 'appuntamenti';

    protected $fillable = [
        'paziente_id',
        'medico_id',
        'data_ora',
        'stato',
        'motivo',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'data_ora' => 'datetime',
        ];
    }

    public function paziente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paziente_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function referto(): HasOne
    {
        return $this->hasOne(Referto::class);
    }

    public function isAnnullabile(): bool
    {
        return $this->stato === self::STATO_PRENOTATO && $this->data_ora->isFuture();
    }
}
