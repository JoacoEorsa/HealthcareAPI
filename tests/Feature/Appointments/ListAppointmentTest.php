<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Lightit\Appointments\App\Controllers\ListAppointmentController;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see ListAppointmentController */
    it('can list appointments successfully', function (): void {
        AppointmentFactory::new()->count(5)->create();

        getJson(url('/api/appointments'))
            ->assertOk()
            ->assertJsonCount(5, 'data');
    });

    it('returns paginated results', function (): void {
        AppointmentFactory::new()->count(3)->create();

        getJson(url('/api/appointments'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    });

    it('orders results by id desc', function (): void {
        $first = AppointmentFactory::new()->createOne();
        $second = AppointmentFactory::new()->createOne();
        $third = AppointmentFactory::new()->createOne();

        getJson(url('/api/appointments'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $third->id)
            ->assertJsonPath('data.1.id', $second->id)
            ->assertJsonPath('data.2.id', $first->id);
    });

    it('is publicly accessible without authentication', function (): void {
        AppointmentFactory::new()->createOne();

        getJson(url('/api/appointments'))
            ->assertOk();
    });
});
