<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\UpdateAppointmentRequest;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\UpsertAppointmentAction;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class UpdateAppointmentController
{
    #[Endpoint(
        operationId: 'updateAppointment',
        title: 'Update a appointment',
        description: 'Updates an existing appointment.'
    )]
    public function __invoke(
        UpdateAppointmentRequest $updateAppointmentRequest,
        Appointment $appointment,
        UpsertAppointmentAction $upsertAppointmentAction,
    ): JsonResponse {
        $doctor = Doctor::query()->findOrFail($updateAppointmentRequest->integer(UpdateAppointmentRequest::DOCTOR_ID));

        /** @var Patient $patient */
        $patient = $updateAppointmentRequest->user();

        $appointment = $upsertAppointmentAction->execute(
            $updateAppointmentRequest->toDto(),
            $doctor,
            $patient,
            $appointment
        );

        return AppointmentResource::make($appointment)
            ->response();
    }
}
