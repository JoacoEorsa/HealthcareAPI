<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Patients\Domain\Models\Patient;

class DeletePatientAction
{
    /**
     * @throws \Throwable
     */
    public function execute(Patient $patient): void
    {
        $patient->appointments()->where('status', AppointmentStatus::Scheduled)
            ->update(['status' => AppointmentStatus::Cancelled]);

        $patient->deleteOrFail();
    }
}
