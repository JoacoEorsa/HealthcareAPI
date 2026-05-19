<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Patients')]
final readonly class GetMeController
{
    #[Endpoint(
        operationId: 'getMe',
        title: 'Get the currently authenticated entity',
        description: 'Returns the currently authenticated patients profile'
    )]
    public function __invoke(#[CurrentUser] Patient $patient): JsonResponse
    {
        return PatientResource::make($patient)
            ->response();
    }
}
