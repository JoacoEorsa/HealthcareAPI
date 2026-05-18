<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Appointments\Domain\Models\Appointment;
use Spatie\QueryBuilder\QueryBuilder;

class ListAppointmentAction
{
    /**
     * @return LengthAwarePaginator<int, Appointment>
     */
    public function execute(): LengthAwarePaginator
    {
        return QueryBuilder::for(Appointment::class)
            ->with(['doctor', 'clinic', 'patient'])
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
