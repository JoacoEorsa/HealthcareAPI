<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;

class UpsertDoctorRequest extends FormRequest
{
    public const string FIRST_NAME = 'first_name';

    public const string LAST_NAME = 'last_name';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::FIRST_NAME => ['required', 'string', 'min:4', 'max:80'],
            self::LAST_NAME => ['required', 'string', 'min:4', 'max:80', ],
        ];
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            firstName: $this->string(self::FIRST_NAME)->toString(),
            lastName: $this->string(self::LAST_NAME)->toString(),
        );
    }
}
