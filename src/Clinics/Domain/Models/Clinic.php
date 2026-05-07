<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;

/**
 * @property int                          $id
 * @property string                       $name
 * @property string                       $address
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Doctor> $doctors
 * @property-read int|null $doctors_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Lightit\Appointments\Domain\Models\Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
