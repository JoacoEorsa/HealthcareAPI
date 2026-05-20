<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;
use Tests\RequestFactories\StoreAppointmentRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

dataset(name: 'validation-rules', dataset: [
    'doctor_id is required' => ['doctor_id', ''],
    'doctor_id must be an integer' => ['doctor_id', 'not-an-integer'],

    'clinic_id is required' => ['clinic_id', ''],
    'clinic_id must be an integer' => ['clinic_id', 'not-an-integer'],

    'starts_at is required' => ['starts_at', ''],
    'starts_at must be a date' => ['starts_at', 'not-a-date'],
    'starts_at after today' => ['starts_at', fn (): string => now()->subDay()->toDateTimeString()],

    'ends_at is required' => ['ends_at', ''],
    'ends_at must be a date' => ['ends_at', 'not-a-date'],
    'ends_at must be after starts_at' => ['ends_at', fn (): string => now()->subDay()->toDateTimeString()],

]);

describe('appointments', function (): void {
    /** @see StoreAppointmentController */
    it(description: 'can create an appointment successfully', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $doctor->clinics()->attach($clinic->id);

        $data = StoreAppointmentRequestFactory::new()->create(['doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id, ]);

        $response = postJson(url('/api/appointments'), $data);

        $appointment = Appointment::query()
            ->where('doctor_id', $data['doctor_id'])
            ->where('clinic_id', $data['clinic_id'])
            ->firstOrFail();

        $appointment->load(['doctor', 'clinic', 'patient']);

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll(
                        AppointmentResource::make($appointment)->resolve()
                    )
                )
            );

        assertDatabaseHas('appointments', [
            'doctor_id' => $data['doctor_id'],
            'clinic_id' => $data['clinic_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ]);
    })->actingAs((fn (): Patient => PatientFactory::new()->createOne()), 'api');
});
