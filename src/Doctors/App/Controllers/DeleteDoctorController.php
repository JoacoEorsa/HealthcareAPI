<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Lightit\Doctors\Domain\Actions\DeleteDoctorAction;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctor')]
final readonly class DeleteDoctorController
{
    #[Endpoint(
        operationId: 'deleteDoctor',
        title: 'Delete a doctor',
        description: 'Deletes a doctor by its ID.'
    )]
    public function __invoke(Doctor $doctor, DeleteDoctorAction $deleteDoctorAction): Response
    {
        $deleteDoctorAction->execute($doctor);

        return response()->noContent();
    }
}
