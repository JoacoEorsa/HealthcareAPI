<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\GetAppointmentAction;
use Lightit\Appointments\Domain\Models\Appointment;

#[Group('Appointments')]
final readonly class GetAppointmentController
{
    #[Endpoint(
        operationId: 'getAppointment',
        title: 'Get a single appointment',
        description: 'Retrieves a appointment by its ID.'
    )]
    public function __invoke(Appointment $appointment, GetAppointmentAction $getAppointmentAction): JsonResponse
    {
        $appointment = $getAppointmentAction->execute($appointment);

        return AppointmentResource::make($appointment)
            ->response();
    }
}
