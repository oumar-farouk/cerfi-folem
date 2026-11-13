<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pointage extends Model
{
    use HasFactory;

    protected $fillable = ['inscription_id', 'jour', 'pointe_a', 'methode', 'pointe_par'];

    protected $casts = [
        'jour' => 'date',
        'pointe_a' => 'datetime',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pointe_par');
    }
}
