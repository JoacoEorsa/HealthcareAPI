<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\StoreAppointmentRequest;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\UpsertAppointmentAction;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class StoreAppointmentController
{
    #[Endpoint(
        operationId: 'storeAppointment',
        title: 'Create an appointment',
        description: 'Creates a new appointment'
    )]
    public function __invoke(
        StoreAppointmentRequest $storeAppointmentRequest,
        UpsertAppointmentAction $upsertAppointmentAction,
    ): JsonResponse {
        $doctor = Doctor::query()->findOrFail($storeAppointmentRequest->integer(StoreAppointmentRequest::DOCTOR_ID));

        /** @var Patient $patient */
        $patient = $storeAppointmentRequest->user();

        $appointment = $upsertAppointmentAction->execute($storeAppointmentRequest->toDto(), $doctor, $patient);

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
