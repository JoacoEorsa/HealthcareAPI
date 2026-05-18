<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;
use Spatie\QueryBuilder\QueryBuilder;

class ListMyAppointmentsAction
{
    /**
     * @return LengthAwarePaginator<int, Appointment>
     */
    public function execute(Patient $patient): LengthAwarePaginator
    {
        return QueryBuilder::for($patient->appointments())
            ->with(['doctor', 'clinic', 'patient'])
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
