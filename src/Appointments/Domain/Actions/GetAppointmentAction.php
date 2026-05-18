<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Lightit\Appointments\Domain\Models\Appointment;

class GetAppointmentAction
{
    public function execute(Appointment $appointment): Appointment
    {
        $appointment->load(['doctor', 'clinic', 'patient']);

        return $appointment;
    }
}
