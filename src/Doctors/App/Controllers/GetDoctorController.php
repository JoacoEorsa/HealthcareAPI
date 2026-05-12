<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctor')]
final readonly class GetDoctorController
{
    #[Endpoint(
        operationId: 'getDoctor',
        title: 'Get a single doctor',
        description: 'Retrieves a doctor by its ID.'
    )]
    public function __invoke(Doctor $doctor): JsonResponse
    {
        $doctor->load(['clinics' => fn (BelongsToMany $query) => $query->wherePivotNull('ended_at')]);

        return DoctorResource::make($doctor)
            ->response();
    }
}
