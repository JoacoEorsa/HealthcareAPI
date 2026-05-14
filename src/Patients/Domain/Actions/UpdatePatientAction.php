<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\UpdatePatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpdatePatientAction
{
    public function execute(Patient $patient, UpdatePatientDto $updatePatientDto): Patient
    {
        $patient->first_name = $updatePatientDto->firstName;
        $patient->last_name = $updatePatientDto->lastName;
        $patient->email = $updatePatientDto->email;

        $patient->saveOrFail();

        return $patient;
    }
}
