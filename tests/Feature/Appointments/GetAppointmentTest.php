<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Appointments\App\Controllers\GetAppointmentController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see GetAppointmentController */
    it('retrieves an appointment and returns a successful response', function (): void {
        actingAs(PatientFactory::new()->createOne(), 'api');

        $appointment = AppointmentFactory::new()->createOne();

        getJson(url("/api/appointments/$appointment->id"))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson =>
                $json->hasAll(['id', 'doctor', 'clinic', 'patient', 'starts_at', 'ends_at', 'status'])
                    ->where('id', $appointment->id)
                    ->where('doctor.id', $appointment->doctor_id)
                    ->where('clinic.id', $appointment->clinic_id)
                    ->where('patient.id', $appointment->patient_id)
                )
            );
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
