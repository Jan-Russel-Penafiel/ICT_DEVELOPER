<?php

declare(strict_types=1);

namespace App\Models;

final class StatusLogModel extends BaseIctModel
{
    protected $table = 'status_logs';
    protected $useTimestamps = false;
    protected $allowedFields = ['entity_type', 'entity_id', 'from_status', 'to_status', 'user_id', 'created_at'];

    public function record(string $entityType, int $entityId, string $toStatus, ?string $fromStatus = null, ?int $userId = null): bool
    {
        return (bool) $this->insert([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function forEntity(string $entityType, int $entityId): array
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
