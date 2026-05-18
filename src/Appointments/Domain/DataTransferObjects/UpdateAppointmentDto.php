<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\DataTransferObjects;

use Carbon\CarbonImmutable;

readonly class UpdateAppointmentDto
{
    public function __construct(
        public int $doctorId,
        public int $clinicId,
        public CarbonImmutable $startTime,
        public CarbonImmutable $endTime,
    ) {
    }
}
