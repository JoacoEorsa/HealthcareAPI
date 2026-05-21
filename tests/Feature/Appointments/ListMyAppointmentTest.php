<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Controllers\ListMyAppointmentController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see ListMyAppointmentController */
    it('lists only the authenticated patient appointments', function (): void {
        $patient = PatientFactory::new()->createOne();
        $otherPatient = PatientFactory::new()->createOne();

        AppointmentFactory::new()->count(2)->create(['patient_id' => $patient->id]);
        AppointmentFactory::new()->count(3)->create(['patient_id' => $otherPatient->id]);

        actingAs($patient, 'api');

        $response = getJson(url('/api/appointments/me'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        /** @var list<array{patient: array{id: int}}> $rows */
        $rows = $response->json('data');
        foreach ($rows as $row) {
            expect($row['patient']['id'])->toBe($patient->id);
        }
    });

    it('returns paginated results ordered by id desc', function (): void {
        $patient = PatientFactory::new()->createOne();

        $first = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);
        $second = AppointmentFactory::new()->createOne(['patient_id' => $patient->id]);

        actingAs($patient, 'api');

        getJson(url('/api/appointments/me'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.id', $second->id)
            ->assertJsonPath('data.1.id', $first->id);
    });

    it('returns 401 when unauthenticated', function (): void {
        getJson(url('/api/appointments/me'))
            ->assertUnauthorized();
    });
});
