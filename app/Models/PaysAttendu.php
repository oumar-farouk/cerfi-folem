<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaysAttendu extends Model
{
    use HasFactory;

    protected $table = 'pays_attendus';

    protected $fillable = ['edition_id', 'nom_pays', 'drapeau'];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
