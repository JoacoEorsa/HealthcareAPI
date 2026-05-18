<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Patients\Domain\Models\Patient;

class StoreAppointmentRequest extends FormRequest
{
    public const string DOCTOR_ID = 'doctor_id';

    public const string CLINIC_ID = 'clinic_id';

    public const string STARTS_AT = 'starts_at';

    public const string ENDS_AT = 'ends_at';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::DOCTOR_ID => ['required', 'integer'],
            self::CLINIC_ID => ['required', 'integer'],
            self::STARTS_AT => ['required', 'date', 'after:now'],
            self::ENDS_AT => ['required', 'date', 'after:starts_at'],
        ];
    }

    public function toDto(): AppointmentDto
    {
        /** @var Patient $patient */
        $patient = $this->user();

        return new AppointmentDto(
            doctorId: $this->integer(self::DOCTOR_ID),
            clinicId: $this->integer(self::CLINIC_ID),
            patientId: $patient->id,
            startTime: CarbonImmutable::parse($this->string(self::STARTS_AT)->toString()),
            endTime: CarbonImmutable::parse($this->string(self::ENDS_AT)->toString()),
        );
    }
}
