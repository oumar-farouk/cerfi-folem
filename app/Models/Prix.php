<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prix extends Model
{
    use HasFactory;

    protected $table = 'prix';

    protected $fillable = ['edition_id', 'region_id', 'profil_id', 'montant'];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }
}
