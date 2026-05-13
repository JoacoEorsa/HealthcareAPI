<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Doctors\Domain\Models\Doctor;
use Spatie\QueryBuilder\QueryBuilder;

class ListDoctorAction
{
    /**
     * @return LengthAwarePaginator<int, Doctor>
     */
    public function execute(): LengthAwarePaginator
    {
        return QueryBuilder::for(Doctor::class)
            ->allowedFilters(['first_name', 'last_name'])
            ->allowedSorts(['first_name', 'last_name'])
            ->with(['clinics' => function (Relation $query): void { $query->wherePivotNull('ended_at');}])
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
