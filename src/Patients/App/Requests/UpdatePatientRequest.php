<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Requests;

use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Email;
use Lightit\Patients\Domain\DataTransferObjects\UpdatePatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpdatePatientRequest extends FormRequest
{
    public const string FIRST_NAME = 'first_name';

    public const string LAST_NAME = 'last_name';

    public const string EMAIL = 'email';

    /**
     * @return array<string, mixed>
     */
    public function rules(#[RouteParameter('patient')] Patient $patient): array
    {
        return [
            self::FIRST_NAME => ['required', 'string', 'min:4', 'max:80'],
            self::LAST_NAME => ['required', 'string', 'min:4', 'max:80'],
            self::EMAIL => [
                'required',
                'max:100',
                Email::default(),
                 Rule::unique(Patient::class, 'email')->ignore($patient->id),
            ],
        ];
    }

    public function toDto(): UpdatePatientDto
    {
        return new UpdatePatientDto(
            firstName: $this->string(self::FIRST_NAME)->toString(),
            lastName: $this->string(self::LAST_NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
        );
    }
}
