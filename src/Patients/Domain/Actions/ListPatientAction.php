<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Patients\Domain\Models\Patient;
use Spatie\QueryBuilder\QueryBuilder;

class ListPatientAction
{
    /**
     * @return LengthAwarePaginator<int, Patient>
     */
    public function execute(): LengthAwarePaginator
    {
        return QueryBuilder::for(Patient::class)
            ->allowedFilters(['first_name', 'last_name', 'email'])
            ->allowedSorts(['first_name', 'last_name', 'email'])
            ->paginate();
    }
}
