<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

/**
 * @property int                          $id
 * @property int                          $doctor_id
 * @property int                          $patient_id
 * @property int                          $clinic_id
 * @property \Carbon\CarbonImmutable      $starts_at
 * @property \Carbon\CarbonImmutable      $ends_at
 * @property string                       $status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Clinic $clinic
 * @property-read Doctor $doctor
 * @property-read Patient $patient
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Appointment extends Model
{
    #[\Override]
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    #[\Override]
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'clinic_id',
        'starts_at',
        'ends_at',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Lightit\Doctors\Domain\Models\Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Lightit\Patients\Domain\Models\Patient, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Lightit\Clinics\Domain\Models\Clinic, $this>
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
