<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Lightit\Clinics\Domain\Models\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doctor extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }
}
