<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;

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
        return [
            'doctor_id' => DoctorFactory::new(),
            'clinic_id' => ClinicFactory::new(),
            'patient_id' => PatientFactory::new(),
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'status' => AppointmentStatus::Scheduled,
        ];
    }

    /**
     * Indicate that the model's status should be cancelled.
     */
    public function cancelled(): static
    {
        return $this->set('status', AppointmentStatus::Cancelled);
    }
}
