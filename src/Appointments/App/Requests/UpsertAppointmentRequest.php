<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;

class UpsertAppointmentRequest extends FormRequest
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
            self::DOCTOR_ID => ['required', Rule::numeric()->integer()],
            self::CLINIC_ID => ['required', Rule::numeric()->integer()],
            self::STARTS_AT => ['required', Rule::date()->after(now())],
            self::ENDS_AT => ['required', Rule::date()->after(self::STARTS_AT)],
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            doctorId: $this->integer(self::DOCTOR_ID),
            clinicId: $this->integer(self::CLINIC_ID),
            startTime: CarbonImmutable::parse($this->string(self::STARTS_AT)->toString()),
            endTime: CarbonImmutable::parse($this->string(self::ENDS_AT)->toString()),
        );
    }
}
