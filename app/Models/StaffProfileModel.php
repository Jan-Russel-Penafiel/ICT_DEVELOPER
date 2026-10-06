<?php

declare(strict_types=1);

namespace App\Models;

final class StaffProfileModel extends BaseIctModel
{
    protected $table = 'staff_profiles';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['user_id', 'campus_id'];

    public function replaceCampusAssignment(int $userId, ?int $campusId): void
    {
        $profile = $this->where('user_id', $userId)->first();

        if ($campusId === null) {
            if ($profile !== null) {
                $this->delete((int) $profile['id']);
            }

            return;
        }

        if ($profile !== null) {
            $this->update((int) $profile['id'], ['campus_id' => $campusId]);
            return;
        }

        $this->insert(['user_id' => $userId, 'campus_id' => $campusId]);
    }

    public function campusIdForUser(int $userId): ?int
    {
        $profile = $this->select('campus_id')->where('user_id', $userId)->first();

        return $profile === null ? null : (int) $profile['campus_id'];
    }
}
