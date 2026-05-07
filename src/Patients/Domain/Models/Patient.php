<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    // public function appointments(): HasMany
    // {
    //     return $this->hasMany(Appointment::class);
    // }
}
