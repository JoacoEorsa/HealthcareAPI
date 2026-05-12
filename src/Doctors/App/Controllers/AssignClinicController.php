<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Requests\AssignClinicRequest;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Actions\AssignClinicAction;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctor')]
final readonly class AssignClinicController
{
    #[Endpoint(
        operationId: 'assignClinic',
        title: 'assign a clinic to a doctor',
        description: 'Updates a doctors clinic list.'
    )]
    public function __invoke(
        Doctor $doctor,
        AssignClinicRequest $request,
        AssignClinicAction $assignClinicAction,
    ): JsonResponse {
        $doctor = $assignClinicAction->execute($doctor, $request->getClinicIds());

        return DoctorResource::make($doctor)
            ->response();
    }
}
