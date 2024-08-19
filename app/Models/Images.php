<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ImagesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Images extends Model
{
    /** @use HasFactory<ImagesFactory> */
    use HasFactory;

    protected $fillable = ['url'];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
