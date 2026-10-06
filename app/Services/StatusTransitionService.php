<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StatusLogModel;
use CodeIgniter\Database\BaseConnection;
use DomainException;

final class StatusTransitionService
{
    public function __construct(private readonly BaseConnection $db)
    {
    }

    /** @param list<string> $allowed */
    public function transition(string $table, int $id, string $to, array $allowed, string $entityType, array $extra = []): void
    {
        if (! in_array($to, $allowed, true)) throw new DomainException('Unsupported status transition.');
        $this->db->transStart();
        $row = $this->db->table($table)->where('id', $id)->get()->getRowArray();
        if ($row === null) throw new DomainException('Ticket not found.');
        $from = (string) $row['status'];
        if ($from === $to) { $this->db->transComplete(); return; }
        $this->db->table($table)->where('id', $id)->update(array_merge($extra, ['status' => $to, 'updated_at' => date('Y-m-d H:i:s')]));
        (new StatusLogModel())->insert([
            'entity_type' => $entityType, 'entity_id' => $id, 'from_status' => $from, 'to_status' => $to,
            'user_id' => auth()->id(), 'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        if (! $this->db->transStatus()) throw new DomainException('Could not record status change.');
    }
}
