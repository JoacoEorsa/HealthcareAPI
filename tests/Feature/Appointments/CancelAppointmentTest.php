<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Controllers\CancelAppointmentController;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;

describe('appointments', function (): void {
    /** @see CancelAppointmentController */
    it('cancels an appointment and returns no content', function (): void {
        $patient = PatientFactory::new()->createOne();
        $appointment = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);
        actingAs($patient, 'api');

        patchJson(url("/api/appointments/$appointment->id"))
            ->assertNoContent();

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
        ]);
    });

    it('can cancel an already-cancelled appointment', function (): void {
        $patient = PatientFactory::new()->createOne();
        $appointment = AppointmentFactory::new()->cancelled()->createOne(['patient_id' => $patient->id]);
        actingAs($patient, 'api');

        patchJson(url("/api/appointments/$appointment->id"))
            ->assertNoContent();

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
        ]);
    });

    it('returns 404 when the appointment is not found', function (): void {
        $patient = PatientFactory::new()->createOne();
        actingAs($patient, 'api');

        patchJson(url('/api/appointments/9999'))
            ->assertNotFound();
    });

    it('returns 401 when unauthenticated', function (): void {
        $appointment = AppointmentFactory::new()->createOne();

        patchJson(url("/api/appointments/$appointment->id"))
            ->assertUnauthorized();
    });
});
