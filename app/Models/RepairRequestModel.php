<?php

declare(strict_types=1);

namespace App\Models;

final class RepairRequestModel extends BaseIctModel
{
    protected $table = 'repair_requests';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['request_no', 'requester', 'contact', 'office', 'equipment', 'serial_no', 'issue', 'status', 'sent_to', 'sent_at', 'returned_at', 'released_at', 'released_to'];

    public function newestFirst(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function equipmentOptions(): array
    {
        return $this->select('equipment')->distinct()->orderBy('equipment')->findAll();
    }

    public function findByRequestNumber(string $requestNumber): ?array
    {
        return $this->where('request_no', $requestNumber)->first();
    }

    public function findForTracking(string $reference): ?array
    {
        return $this->select('id, request_no reference_no, status, created_at, sent_at, returned_at, released_at, equipment')
            ->where('request_no', $reference)
            ->first();
    }
}
