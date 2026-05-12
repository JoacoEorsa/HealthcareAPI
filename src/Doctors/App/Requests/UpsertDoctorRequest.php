<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;

class UpsertDoctorRequest extends FormRequest
{
    public const string FIRSTNAME = 'first_name';

    public const string LASTNAME = 'last_name';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::FIRSTNAME => ['required', 'string', 'min:4', 'max:80'],
            self::LASTNAME => ['required', 'string', 'min:4', 'max:80', ],
        ];
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            firstName: $this->string(self::FIRSTNAME)->toString(),
            lastName: $this->string(self::LASTNAME)->toString(),
        );
    }
}
