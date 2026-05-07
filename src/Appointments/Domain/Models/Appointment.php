<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Clinics\Domain\Models\Clinic;
use Illuminate\Database\Eloquent\Model;

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
        'status'
    ];


    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

}
