<?php

declare(strict_types=1);

namespace App\Models;

final class StaffProfileModel extends BaseIctModel
{
    protected $table = 'staff_profiles';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['user_id', 'campus_id', 'deleted_at'];

    public function replaceCampusAssignment(int $userId, ?int $campusId): void
    {
        $profile = $this->withDeleted()->where('user_id', $userId)->first();

        if ($campusId === null) {
            // "No campus restriction" means access to all campuses.
            // Keep the profile row with a null campus_id so the account
            // list resolves to "All campuses" instead of a stale value.
            if ($profile !== null) {
                $this->withDeleted()->update((int) $profile['id'], ['campus_id' => null, 'deleted_at' => null]);
            }

            return;
        }

        if ($profile !== null) {
            $this->withDeleted()->update((int) $profile['id'], ['campus_id' => $campusId, 'deleted_at' => null]);
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
