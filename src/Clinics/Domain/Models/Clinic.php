<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Models;

use Lightit\Doctors\Domain\Models\Doctor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function doctors(): BelongsToMany
     {
         return $this->belongsToMany(Doctor::class);
     }
}
