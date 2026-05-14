<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\Models\Patient;

class DeletePatientAction
{
    public function execute(Patient $patient): void
    {
        $patient->deleteOrFail();
    }
}
