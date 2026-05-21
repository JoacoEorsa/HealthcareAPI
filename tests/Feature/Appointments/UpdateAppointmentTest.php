<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Controllers\UpdateAppointmentController;
use Tests\RequestFactories\UpdateAppointmentRequestFactory;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

dataset('update-validation-rules', [
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
    /** @see UpdateAppointmentController */
    it('can update an appointment successfully', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);

        $appointment = AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
        ]);

        actingAs($patient, 'api');

        $newStartsAt = now()->addDays(2);
        $newEndsAt = $newStartsAt->copy()->addHour();

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => $newStartsAt->toDateTimeString(),
            'ends_at' => $newEndsAt->toDateTimeString(),
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertOk()
            ->assertJsonPath('data.id', $appointment->id)
            ->assertJsonPath('data.doctor.id', $doctor->id)
            ->assertJsonPath('data.clinic.id', $clinic->id)
            ->assertJsonPath('data.patient.id', $patient->id);

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ]);
    });

    it('cannot update an appointment with invalid data', function (string $field, mixed $value): void {
        $patient = PatientFactory::new()->createOne();
        $appointment = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);
        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([$field => $value]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field], 'error.fields');
    })->with('update-validation-rules');

    it('cannot update to overlap with another doctor appointment', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $otherPatient = PatientFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);

        $appointment = AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
        ]);

        AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $otherPatient->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
        ]);

        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => now()->addDay()->toDateTimeString(),
            'ends_at' => now()->addDay()->addHour()->toDateTimeString(),
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertUnprocessable();
    });

    it('cannot update to overlap with another patient appointment', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $otherDoctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);
        $otherDoctor->clinics()->attach($clinic->id);

        $appointment = AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
        ]);

        AppointmentFactory::new()->createOne([
            'doctor_id' => $otherDoctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
        ]);

        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => now()->addDay()->toDateTimeString(),
            'ends_at' => now()->addDay()->addHour()->toDateTimeString(),
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertUnprocessable();
    });

    it('allows updating an appointment without changing its time slot', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);

        $startsAt = now()->addDay();
        $endsAt = $startsAt->copy()->addHour();

        $appointment = AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => $startsAt->toDateTimeString(),
            'ends_at' => $endsAt->toDateTimeString(),
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertOk();
    });

    it('cannot update when the doctor is not assigned to the clinic', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);

        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertUnprocessable();
    });

    it('cannot update when the doctor does not exist', function (): void {
        $patient = PatientFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $appointment = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);
        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create([
            'doctor_id' => 9999,
            'clinic_id' => $clinic->id,
        ]);

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertNotFound();
    });

    it('returns 404 when the appointment is not found', function (): void {
        $patient = PatientFactory::new()->createOne();
        actingAs($patient, 'api');

        $data = UpdateAppointmentRequestFactory::new()->create();

        putJson(url('/api/appointments/9999'), $data)
            ->assertNotFound();
    });

    it('returns 401 when unauthenticated', function (): void {
        $appointment = AppointmentFactory::new()->createOne();

        $data = UpdateAppointmentRequestFactory::new()->create();

        putJson(url("/api/appointments/$appointment->id"), $data)
            ->assertUnauthorized();
    });
});
