<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lightit\Appointments\Domain\DataTransferObjects\UpdateAppointmentDto;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Shared\App\Exceptions\Http\InvalidActionException;

class UpdateAppointmentAction
{
    public function execute(
        UpdateAppointmentDto $updateAppointmentDto,
        Appointment $appointment,
        Doctor $doctor,
    ): Appointment {
        $appointment->load(['patient']);

        $this->isDoctorAssignedToClinic($doctor, $updateAppointmentDto);

        $this->hasOverlappingAppointments($doctor->appointments(), $updateAppointmentDto, $appointment->id);

        /** @var Patient $patient */
        $patient = $appointment->patient;
        $this->hasOverlappingAppointments(
            $patient->appointments(),
            $updateAppointmentDto,
            $appointment->id
        );

        $appointment->doctor_id = $updateAppointmentDto->doctorId;
        $appointment->clinic_id = $updateAppointmentDto->clinicId;
        $appointment->starts_at = $updateAppointmentDto->startTime;
        $appointment->ends_at = $updateAppointmentDto->endTime;

        $appointment->saveOrFail();

        $appointment->load(['doctor', 'clinic', 'patient']);

        return $appointment;
    }

    private function isDoctorAssignedToClinic(Doctor $doctor, UpdateAppointmentDto $updateAppointmentDto): void
    {
        if (! $doctor->clinics()->wherePivotNull('ended_at')
            ->where('clinics.id', $updateAppointmentDto->clinicId)
            ->exists()) {
            throw new InvalidActionException();
        }
    }

    /**
     * @param HasMany<Appointment, Doctor>|HasMany<Appointment, Patient> $appointments
     */
    private function hasOverlappingAppointments(
        HasMany $appointments,
        UpdateAppointmentDto $updateAppointmentDto,
        int $appointmentId,
    ): void {
        if ($appointments
            ->where('id', '!=', $appointmentId)
            ->where('status', AppointmentStatus::Scheduled)
            ->where('ends_at', '>', $updateAppointmentDto->startTime)
            ->where('starts_at', '<', $updateAppointmentDto->endTime)
            ->exists()) {
            throw new InvalidActionException();
        }
    }
}
