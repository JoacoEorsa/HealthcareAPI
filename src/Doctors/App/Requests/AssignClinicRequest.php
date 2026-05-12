<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Clinics\Domain\Models\Clinic;

class AssignClinicRequest extends FormRequest
{
    public const string CLINIC_IDS = 'clinic_ids';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::CLINIC_IDS => ['required', 'array', Rule::exists(Clinic::class, 'id')],
            self::CLINIC_IDS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function getClinicIds(): array
    {
        return $this->array(self::CLINIC_IDS);
    }
}
