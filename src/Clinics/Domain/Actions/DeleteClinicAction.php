<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Lightit\Clinics\Domain\Models\Clinic;

class DeleteClinicAction
{
    public function execute(Clinic $clinic): void
    {
        $clinic->doctors()->detach();

        $clinic->delete();
    }
}
