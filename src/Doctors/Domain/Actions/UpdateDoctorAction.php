<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpdateDoctorAction
{
    public function execute(Doctor $doctor, DoctorDto $doctorDto): Doctor
    {
        $doctor->first_name = $doctorDto->firstName;
        $doctor->last_name = $doctorDto->lastName;

        $doctor->saveOrFail();

        $doctor->load('clinics');

        return $doctor;
    }
}
