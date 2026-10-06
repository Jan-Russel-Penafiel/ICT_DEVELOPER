<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RepairRequestModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

final class RepairRequestService
{
    public function __construct(private readonly RepairRequestModel $requests = new RepairRequestModel()) {}

    public function indexData(): array { return ['requests' => $this->requests->newestFirst(), 'equipment' => $this->requests->equipmentOptions()]; }
    public function update(int $id, array $data): void
    {
        if (! $this->requests->find($id)) throw PageNotFoundException::forPageNotFound();
        $this->requests->update($id, ['requester'=>trim((string)$data['requester']),'contact'=>trim((string)$data['contact']),'office'=>trim((string)$data['office']),'equipment'=>trim((string)$data['equipment']),'serial_no'=>trim((string)$data['serial_no']) ?: null,'issue'=>trim((string)$data['issue'])]);
    }
    public function archive(int $id): void { if (! $this->requests->find($id)) throw PageNotFoundException::forPageNotFound(); $this->requests->delete($id); }
    public function transition(int $id, string $status, array $data): void
    {
        $current = $this->requests->find($id); if (! $current) throw PageNotFoundException::forPageNotFound();
        $flow = ['received'=>['assessed','rejected'],'assessed'=>['sent','rejected'],'sent'=>['returned'],'returned'=>['released']];
        if (! in_array($status, $flow[$current['status']] ?? [], true)) throw new RuntimeException('That status change is not allowed.');
        $extra = [];
        if ($status === 'sent') { $extra = ['sent_to'=>trim((string) ($data['sent_to'] ?? '')),'sent_at'=>date('Y-m-d H:i:s')]; if ($extra['sent_to'] === '') throw new RuntimeException('Destination is required.'); }
        if ($status === 'returned') $extra = ['returned_at'=>date('Y-m-d H:i:s')];
        if ($status === 'released') { $extra = ['released_at'=>date('Y-m-d H:i:s'),'released_to'=>trim((string) ($data['released_to'] ?? ''))]; if ($extra['released_to'] === '') throw new RuntimeException('Receiver name is required.'); }
        (new StatusTransitionService(db_connect()))->transition('repair_requests', $id, $status, ['assessed','sent','returned','released','rejected'], 'repair_request', $extra);
    }
}
