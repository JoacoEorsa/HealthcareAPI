<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\Models\Doctor;

class DeleteDoctorAction
{
    public function execute(Doctor $doctor): void
    {
        $doctor->clinics()->detach();

        $doctor->delete();
    }
}
