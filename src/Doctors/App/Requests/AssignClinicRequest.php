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
            self::CLINIC_IDS => ['required', 'array'],
            self::CLINIC_IDS . '.*' => ['integer', Rule::exists(Clinic::class, 'id')],
        ];
    }

    /**
     * @return array<int, int>
     */
    public function getClinicIds(): array
    {
        return array_values(
            array_map(fn (mixed $id): int => is_numeric($id) ? (int) $id : 0, $this->array(self::CLINIC_IDS))
        );
    }
}
