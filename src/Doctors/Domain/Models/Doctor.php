<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\DoctorsClinics\Domain\Models\DoctorClinic;

/**
 * @property int                          $id
 * @property string                       $first_name
 * @property string                       $last_name
 * @property \Carbon\CarbonImmutable      $created_at
 * @property \Carbon\CarbonImmutable      $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read DoctorClinic|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Clinic> $clinics
 * @property-read int|null $clinics_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Doctor extends Model
{
    use SoftDeletes;

    #[\Override]
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Lightit\Clinics\Domain\Models\Clinic, $this, \Lightit\DoctorsClinics\Domain\Models\DoctorClinic>
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'doctor_clinic')
            ->using(DoctorClinic::class)
            ->withPivot('ended_at')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Lightit\Appointments\Domain\Models\Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
