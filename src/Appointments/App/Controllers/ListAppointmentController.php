<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\ListAppointmentAction;

#[Group('Appointments')]
final readonly class ListAppointmentController
{
    #[Endpoint(
        operationId: 'listAppointment',
        title: 'List appointment',
        description: 'Retrieves a list of appointments.'
    )]
    public function __invoke(
        ListAppointmentAction $listAppointmentAction,
    ): JsonResponse {
        $appointments = $listAppointmentAction->execute();

        return AppointmentResource::collection($appointments)
            ->response();
    }
}
