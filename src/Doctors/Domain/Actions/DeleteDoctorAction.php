<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Doctors\Domain\Models\Doctor;

class DeleteDoctorAction
{
    public function execute(Doctor $doctor): void
    {
        $doctor->appointments()->where('status', AppointmentStatus::Scheduled)
            ->update(['status' => AppointmentStatus::Cancelled]);
        $doctor->clinics()->detach();

        $doctor->deleteOrFail();
    }
}
