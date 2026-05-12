<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\Models\Doctor;

class AssignClinicAction
{
    public function execute(Doctor $doctor, array $clinicIds): Doctor
    {
        $doctor->clinics()->sync($clinicIds);

        $doctor->load('clinics');

        return $doctor;
    }
}
