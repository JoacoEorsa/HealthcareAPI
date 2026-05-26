<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName()
        ];
    }

    public function withClinic(Clinic $clinic): static{
        return $this->hasAttached($clinic, [], 'clinics');
    }

    public function withExpiredClinicAssigment(Clinic $clinic): static{
        return $this->hasAttached($clinic, ['ended_at' => now()->subDay()], 'clinics');
    }

}
