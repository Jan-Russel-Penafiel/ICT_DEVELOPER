<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ResetRequestModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

final class ResetTicketService
{
    public function __construct(private readonly ResetRequestModel $requests = new ResetRequestModel()) {}

    public function all(): array { return $this->requests->newestFirst(); }

    public function update(int $id, array $data): void
    {
        if (! $this->requests->find($id)) throw PageNotFoundException::forPageNotFound();
        $this->requests->update($id, ['student_no'=>trim((string)$data['student_no']),'requester_name'=>trim((string)$data['requester_name']),'contact'=>trim((string)$data['contact']),'channel'=>(string)$data['channel']]);
    }

    public function archive(int $id): void
    {
        if (! $this->requests->find($id)) throw PageNotFoundException::forPageNotFound();
        $this->requests->delete($id);
    }

    public function transition(int $id, string $status, ?int $userId): void
    {
        $current = $this->requests->find($id);
        if (! $current) throw PageNotFoundException::forPageNotFound();
        $flow = ['requested'=>['verified','rejected'],'verified'=>['reset','rejected'],'reset'=>['closed']];
        if (! in_array($status, $flow[$current['status']] ?? [], true)) throw new RuntimeException('That status change is not allowed.');
        (new StatusTransitionService(db_connect()))->transition('reset_requests', $id, $status, ['verified','reset','closed','rejected'], 'reset_request', $status === 'verified' ? ['verified_by' => $userId] : []);
    }
}
