<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpsertDoctorAction
{
    public function execute(DoctorDto $doctorDto, Doctor|null $doctor = null): Doctor
    {
        $doctor = $doctor ?? new Doctor();
        $doctor->first_name = $doctorDto->firstName;
        $doctor->last_name = $doctorDto->lastName;

        $doctor->saveOrFail();

        $doctor->load(['clinics' => fn (BelongsToMany $query) => $query->wherePivotNull('ended_at')]);

        return $doctor;
    }
}
