<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Controllers\GetAppointmentController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see GetAppointmentController */
    it('retrieves an appointment and returns a successful response', function (): void {
        $patient = PatientFactory::new()->createOne();
        actingAs($patient, 'api');

        $appointment = AppointmentFactory::new()->createOne();

        getJson(url("/api/appointments/$appointment->id"))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'doctor',
                    'clinic',
                    'patient',
                    'starts_at',
                    'ends_at',
                    'status',
                ],
            ])
            ->assertJsonPath('data.id', $appointment->id)
            ->assertJsonPath('data.doctor.id', $appointment->doctor_id)
            ->assertJsonPath('data.clinic.id', $appointment->clinic_id)
            ->assertJsonPath('data.patient.id', $appointment->patient_id);
    });

    it('returns a 404 response when the appointment is not found', function (): void {
        $patient = PatientFactory::new()->createOne();
        actingAs($patient, 'api');

        getJson(url('/api/appointments/9999'))
            ->assertNotFound();
    });

    it('returns 401 when unauthenticated', function (): void {
        $appointment = AppointmentFactory::new()->createOne();

        getJson(url("/api/appointments/$appointment->id"))
            ->assertUnauthorized();
    });
});
