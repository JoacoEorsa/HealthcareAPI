<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Patients\Domain\Models\Patient;

/**
 * @mixin Patient
 */
#[SchemaName('Patient')]
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email_address' => $this->email,
        ];
    }
}
