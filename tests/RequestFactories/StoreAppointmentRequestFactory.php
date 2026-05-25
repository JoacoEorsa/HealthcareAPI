<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StoreAppointmentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'doctor_id' => 1,
            'clinic_id' => 1,
            'starts_at' => now()->addDay()->toDateTimeString(),
            'ends_at' => now()->addDay()->addHour()->toDateTimeString(),
        ];
    }

    public function aWeekFromNow(): static
    {
        return $this->state([
            'starts_at' => now()->addWeek()->toDateTimeString(),
            'ends_at' => now()->addWeek()->addHour()->toDateTimeString(),
        ]);
    }

    public function withDoctorAndClinic(int $doctorId, int $clinicId): static
    {
        return $this->state([
            'doctor_id' => $doctorId,
            'clinic_id' => $clinicId,
        ]);
    }
}
