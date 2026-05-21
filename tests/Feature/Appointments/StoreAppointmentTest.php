<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\Domain\Models\Appointment;
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
            ->assertJsonPath('data.doctor.id', $doctor->id)
            ->assertJsonPath('data.clinic.id', $clinic->id)
            ->assertJsonPath('data.patient.id', $patient->id);

        assertDatabaseHas('appointments', [
            'doctor_id' => $data['doctor_id'],
            'clinic_id' => $data['clinic_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ]);
    });

    it(
        description: 'cannot create an appointment when doctor is not assigned to the clinic',
        closure: function (): void {
            $doctor = DoctorFactory::new()->createOne();
            $clinic = ClinicFactory::new()->createOne();
            $patient = PatientFactory::new()->createOne();
            $this->actingAs($patient, 'api');
    
            $data = StoreAppointmentRequestFactory::new()->create(['doctor_id' => $doctor->id,
                'clinic_id' => $clinic->id, ]);
    
            $response = postJson(url('/api/appointments'), $data);
    
            $response
                ->assertUnprocessable();
        }
    );

    it(description: 'cannot create an overlapping appointment for a doctor', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');
        $patient2 = PatientFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);

        AppointmentFactory::new()->createOne(['doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id, 'patient_id' => $patient2->id,
            'starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

        $data = StoreAppointmentRequestFactory::new()->create(['doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id, ]);

        $response = postJson(url('/api/appointments'), $data);

        $response
            ->assertUnprocessable();
    });

    it(description: 'cannot create an overlapping appointment for a patient', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $doctor2 = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);
        $doctor2->clinics()->attach($clinic->id);

        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');


        AppointmentFactory::new()->createOne(['doctor_id' => $doctor2->id,
            'clinic_id' => $clinic->id, 'patient_id' => $patient->id,
            'starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

        $data = StoreAppointmentRequestFactory::new()->create(['doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id, ]);

        $response = postJson(url('/api/appointments'), $data);

        $response
            ->assertUnprocessable();
    });

    it(description: 'cannot create an appointment if patient is unauthenticated', closure: function (): void {
        $data = StoreAppointmentRequestFactory::new()->create();

        $response = postJson(url('/api/appointments'), $data);

        $response
            ->assertUnauthorized();
    });

    it('cannot create an appointment with invalid data', function (string $field, mixed $value): void {
        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $data = StoreAppointmentRequestFactory::new()->create([$field => $value]);

        postJson(url('/api/appointments'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field], 'error.fields');
    })->with('validation-rules');

    it('cannot create an appointment when the doctor does not exist', function (): void {
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $data = StoreAppointmentRequestFactory::new()->create([
            'doctor_id' => 9999,
            'clinic_id' => $clinic->id,
        ]);

        postJson(url('/api/appointments'), $data)
            ->assertNotFound();
    });

    it('allows back-to-back non-overlapping appointments for the same doctor', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $otherPatient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $doctor->clinics()->attach($clinic->id);

        $startsAt = now()->addDay();
        $endsAt = $startsAt->copy()->addHour();

        AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $otherPatient->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $data = StoreAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => $endsAt->toDateTimeString(),
            'ends_at' => $endsAt->copy()->addHour()->toDateTimeString(),
        ]);

        postJson(url('/api/appointments'), $data)
            ->assertCreated();
    });

    it('ignores cancelled appointments when checking for overlap', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $otherPatient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $doctor->clinics()->attach($clinic->id);

        AppointmentFactory::new()->cancelled()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'patient_id' => $otherPatient->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
        ]);

        $data = StoreAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
        ]);

        postJson(url('/api/appointments'), $data)
            ->assertCreated();
    });

    it('cannot create an appointment when the doctor clinic assignment has ended', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();
        $this->actingAs($patient, 'api');

        $doctor->clinics()->attach($clinic->id, ['ended_at' => now()->subDay()]);

        $data = StoreAppointmentRequestFactory::new()->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
        ]);

        postJson(url('/api/appointments'), $data)
            ->assertUnprocessable();
    });
});
