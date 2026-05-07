<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Clinics\Domain\Models\Clinic;

class Doctor extends Model
{
    #[\Override]
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Lightit\Clinics\Domain\Models\Clinic, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }
}
