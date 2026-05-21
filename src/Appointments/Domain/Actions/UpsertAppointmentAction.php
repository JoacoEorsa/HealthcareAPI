<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Shared\App\Exceptions\Http\InvalidActionException;

class UpsertAppointmentAction
{
    public function execute(
        AppointmentDto $appointmentDto,
        Patient $patient,
        Appointment|null $appointment = null,
    ): Appointment {
        $doctor = Doctor::query()->findOrFail($appointmentDto->doctorId);

        $this->isDoctorAssignedToClinic($doctor, $appointmentDto);
        $this->hasOverlappingAppointments($doctor->appointments(), $appointmentDto, $appointment?->id);
        $this->hasOverlappingAppointments($patient->appointments(), $appointmentDto, $appointment?->id);

        if (! $appointment instanceof Appointment) {
            $appointment = new Appointment();
            $appointment->patient_id = $patient->id;
            $appointment->status = AppointmentStatus::Scheduled;
        }

        $appointment->doctor_id = $appointmentDto->doctorId;
        $appointment->clinic_id = $appointmentDto->clinicId;
        $appointment->starts_at = $appointmentDto->startTime;
        $appointment->ends_at = $appointmentDto->endTime;

        $appointment->saveOrFail();

        $appointment->load(['doctor', 'clinic', 'patient']);

        return $appointment;
    }

    private function isDoctorAssignedToClinic(Doctor $doctor, AppointmentDto $appointmentDto): void
    {
        if (! $doctor->clinics()->wherePivotNull('ended_at')
            ->where('clinics.id', $appointmentDto->clinicId)
            ->exists()) {
            throw new InvalidActionException();
        }
    }

    /**
     * @param HasMany<Appointment, Doctor>|HasMany<Appointment, Patient> $appointments
    */
    private function hasOverlappingAppointments(
        HasMany $appointments,
        AppointmentDto $appointmentDto,
        int|null $appointmentId = null,
    ): void {
        if ($appointments
            ->when($appointmentId !== null, fn ($query) => $query->whereKeyNot($appointmentId))
            ->where('status', AppointmentStatus::Scheduled)
            ->where('ends_at', '>', $appointmentDto->startTime)
            ->where('starts_at', '<', $appointmentDto->endTime)
            ->exists()) {
            throw new InvalidActionException();
        }
    }
}
