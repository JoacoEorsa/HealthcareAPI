<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lightit\Doctors\Domain\Models\Doctor;

class AssignClinicAction
{
    /** @param array<int, int> $clinicIds */
    public function execute(Doctor $doctor, array $clinicIds): Doctor
    {
        /** @var array<int, int> $currentClinicIds */
        $currentClinicIds = $doctor->clinics()->wherePivotNull('ended_at')->pluck('clinics.id')->toArray();
        $clinicIdsToRemove = array_diff($currentClinicIds, $clinicIds);
        $clinicIdsToAdd = array_diff($clinicIds, $currentClinicIds);

        $doctor->clinics()->newPivotStatement()
            ->where('doctor_id', $doctor->id)
            ->whereIn('clinic_id', $clinicIdsToRemove)
            ->update(['ended_at' => now()]);

        $doctor->clinics()->attach($clinicIdsToAdd);

        $doctor->load(['clinics' => function (BelongsToMany $query): void { $query->wherePivotNull('ended_at');}]);

        return $doctor;
    }
}
