<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;

class StorePatientRequest extends FormRequest
{
    public const string FIRST_NAME = 'first_name';

    public const string LAST_NAME = 'last_name';

    public const string EMAIL = 'email';

    public const string PASSWORD = 'password';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::FIRST_NAME => ['required', 'string', 'min:4', 'max:80'],
            self::LAST_NAME => ['required', 'string', 'min:4', 'max:80'],
            self::EMAIL => [
                'required',
                'max:100',
                Email::default(),
                Rule::unique(Patient::class, 'email'),
            ],
            self::PASSWORD => [
                'required',
                Password::default(),
                'confirmed',
            ],
        ];
    }

    public function toDto(): PatientDto
    {
        return new PatientDto(
            firstName: $this->string(self::FIRST_NAME)->toString(),
            lastName: $this->string(self::LAST_NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
            password: $this->string(self::PASSWORD)->toString(),
        );
    }
}
