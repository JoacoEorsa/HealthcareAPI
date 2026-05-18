<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\ListMyAppointmentsAction;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class ListMyAppointmentController
{
    #[Endpoint(
        operationId: 'listAppointment',
        title: 'List appointment',
        description: 'Retrieves a list of appointments.'
    )]
    public function __invoke(Request $request, ListMyAppointmentsAction $listMyAppointmentsAction): JsonResponse
    {
        /** @var Patient $patient */
        $patient = $request->user();

        $appointments = $listMyAppointmentsAction->execute($patient);

        return AppointmentResource::collection($appointments)
            ->response();
    }
}
