<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Doctors\Domain\Models\Doctor;

class GetDoctorAction
{
    public function execute(Doctor $doctor): Doctor
    {
        $doctor->load(['clinics' => fn (BelongsToMany $query) => $query->wherePivotNull('ended_at')]);

        return $doctor;
    }
}
