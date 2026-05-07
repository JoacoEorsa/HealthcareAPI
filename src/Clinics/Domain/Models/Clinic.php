<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Doctors\Domain\Models\Doctor;

class Clinic extends Model
{
    #[\Override]
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Lightit\Doctors\Domain\Models\Doctor, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }
}
