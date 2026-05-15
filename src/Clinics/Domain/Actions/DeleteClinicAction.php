<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Clinics\Domain\Models\Clinic;

class DeleteClinicAction
{
    public function execute(Clinic $clinic): void
    {
        $clinic->appointments()->where('status', AppointmentStatus::Scheduled)
            ->update(['status' => AppointmentStatus::Cancelled]);
        $clinic->doctors()->detach();

        $clinic->deleteOrFail();
    }
}
