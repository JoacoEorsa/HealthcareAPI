<?php

declare(strict_types=1);

namespace Lightit\DoctorsClinics\Domain\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int                          $id
 * @property int                          $doctor_id
 * @property int                          $clinic_id
 * @property \Carbon\CarbonImmutable      $created_at
 * @property \Carbon\CarbonImmutable      $updated_at
 * @property \Carbon\CarbonImmutable|null $ended_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DoctorClinic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DoctorClinic extends Pivot
{
    #[\Override]
    protected $guarded = ['id'];

    #[\Override]
    protected $casts = [
        'ended_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }
}
