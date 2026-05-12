<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class StoreDoctorAction
{
    public function execute(DoctorDto $doctorDto): Doctor
    {
        $doctor = new Doctor();

        $doctor->first_name = $doctorDto->firstName;
        $doctor->last_name = $doctorDto->lastName;

        $doctor->saveOrFail();

        $doctor->load('clinics');

        return $doctor;
    }
}
