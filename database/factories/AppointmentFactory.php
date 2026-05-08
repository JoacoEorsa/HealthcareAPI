<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Database\Factories\DoctorFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\PatientFactory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+1 month');
        return [
            'doctor_id' => DoctorFactory::new(),
            'clinic_id' => ClinicFactory::new(),
            'patient_id' => PatientFactory::new(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+1 hour'),
            'status' => AppointmentStatus::Scheduled,
        ];
    }

    /**
     * Indicate that the model's status should be cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AppointmentStatus::Cancelled,
        ]);
    }
}
