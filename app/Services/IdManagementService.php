<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CampusModel;
use App\Models\IdRequestModel;
use App\Models\RegistrationLinkModel;
use App\Models\StatusLogModel;
use App\Models\StudentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

final class IdManagementService
{
    public function __construct(
        private readonly IdRequestModel $requests = new IdRequestModel(),
        private readonly RegistrationLinkModel $links = new RegistrationLinkModel(),
        private readonly CampusModel $campuses = new CampusModel(),
        private readonly StudentModel $students = new StudentModel(),
        private readonly StatusLogModel $statusLogs = new StatusLogModel(),
    ) {
    }

    public function queueData(?int $campusId, bool $registrarReleaseQueueOnly): array
    {
        return [
            'requests' => $this->requests->accessibleRequests($campusId, $registrarReleaseQueueOnly),
            'campuses' => $this->campuses->alphabetical(),
        ];
    }

    public function linksData(): array
    {
        return ['campuses' => $this->campuses->alphabetical(), 'links' => $this->links->newestWithCampus()];
    }

    public function createLink(array $data, ?int $userId): void
    {
        $this->assertCampus((int) $data['campus_id']);
        $this->links->insert($this->linkData($data) + [
            'token' => bin2hex(random_bytes(24)),
            'created_by' => $userId,
        ]);
    }

    public function updateLink(int $id, array $data): void
    {
        $this->findLink($id);
        $this->assertCampus((int) $data['campus_id']);
        $this->links->update($id, $this->linkData($data) + ['is_active' => (int) $data['is_active']]);
    }

    public function archiveLink(int $id): void
    {
        $this->findLink($id);
        $this->links->delete($id);
    }

    public function linkForQr(int $id, ?int $campusId): array
    {
        $link = $this->findLink($id);
        if ($campusId !== null && (int) $link['campus_id'] !== $campusId) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $link;
    }

    public function updateRequest(int $id, array $data, ?int $campusId): void
    {
        $request = $this->findRequest($id, $campusId);
        $studentData = $this->studentData($data);

        if ($this->students->studentNumberExistsForCampus($studentData['student_no'], (int) $request['campus_id'], (int) $request['student_id'])) {
            throw new RuntimeException('That student number already exists in this campus.');
        }

        $this->students->update((int) $request['student_id'], $studentData);
    }

    public function updateReplacement(int $id, string $orNumber, ?int $campusId): void
    {
        $this->findRequest($id, $campusId, 'replacement');
        $this->requests->update($id, ['or_number' => trim($orNumber)]);
    }

    public function archiveRequest(int $id, ?int $campusId): void
    {
        $this->findRequest($id, $campusId);
        $this->requests->delete($id);
    }

    public function changeStatus(int $id, string $toStatus, string $receivedBy, ?int $campusId, bool $registrarOnly, ?int $userId): void
    {
        $request = $this->findRequest($id, $campusId, null, $registrarOnly);
        $flow = [
            'submitted' => ['verified', 'needs_correction', 'rejected'],
            'needs_correction' => ['verified', 'rejected'],
            'verified' => ['printed'],
            'paid' => ['printed'],
            'printed' => ['released'],
        ];

        if (! in_array($toStatus, $flow[$request['status']] ?? [], true)) {
            throw new RuntimeException('That status change is not allowed for this request.');
        }

        $allowedStatuses = $registrarOnly
            ? ['released']
            : ['verified', 'needs_correction', 'printed', 'released', 'rejected'];
        if (! in_array($toStatus, $allowedStatuses, true)) {
            throw new RuntimeException('That status change is not allowed for this role.');
        }

        $extra = [];
        if ($toStatus === 'printed') {
            $extra['printed_at'] = date('Y-m-d H:i:s');
        }
        if ($toStatus === 'released') {
            if ($request['type'] !== 'replacement' && ($request['release_date'] === null || $request['release_date'] > date('Y-m-d'))) {
                throw new RuntimeException('This ID is not yet available for release.');
            }
            if (trim($receivedBy) === '') {
                throw new RuntimeException('Receiver name is required.');
            }
            $extra = ['released_at' => date('Y-m-d H:i:s'), 'received_by' => trim($receivedBy)];
        }

        $db = db_connect();
        $db->transStart();
        $this->requests->update($id, $extra + ['status' => $toStatus]);
        $this->statusLogs->record('id_request', $id, $toStatus, (string) $request['status'], $userId);
        $db->transComplete();

        if (! $db->transStatus()) {
            throw new RuntimeException('Could not record status change.');
        }
    }

    public function replacementsData(?int $campusId): array
    {
        return ['requests' => $this->requests->accessibleRequests($campusId, false, 'replacement')];
    }

    public function replacementReceiptForPrint(int $id, ?int $campusId): array
    {
        $request = $this->findRequest($id, $campusId, 'replacement');
        if (trim((string) $request['or_number']) === '') {
            throw new RuntimeException('This replacement does not have an official receipt.');
        }

        return $request;
    }

    public function recordReplacement(array $data, ?int $campusId, ?int $userId): void
    {
        $student = $this->students->findByStudentNumber(trim((string) $data['student_no']), $campusId);
        if ($student === null) {
            throw new RuntimeException('Student was not found in your campus.');
        }

        $now = date('Y-m-d H:i:s');
        $orNumber = $this->generateOfficialReceiptNumber();
        $db = db_connect();
        $db->transStart();
        $request = $this->requests->where('student_id', $student['id'])
            ->where('type', 'replacement')
            ->whereIn('status', ['submitted', 'processing'])
            ->first();
        if ($request !== null) {
            $id = (int) $request['id'];
            $this->requests->update($id, [
                'status' => 'paid',
                'or_number' => $orNumber,
                'paid_at' => $now,
            ]);
        } else {
            $id = $this->requests->insert([
                'student_id' => $student['id'],
                'reference_no' => 'IDR-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                'type' => 'replacement',
                'status' => 'paid',
                'or_number' => $orNumber,
                'paid_at' => $now,
            ], true);
        }
        $this->statusLogs->record('id_request', (int) $id, 'paid', $request['status'] ?? null, $userId);
        $db->transComplete();

        if (! $db->transStatus()) {
            throw new RuntimeException('Replacement payment could not be recorded.');
        }
    }

    private function generateOfficialReceiptNumber(): string
    {
        return 'OR-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function productionRows(?int $campusId): array
    {
        return array_values(array_filter(
            $this->requests->accessibleRequests($campusId),
            static fn (array $request): bool => in_array($request['status'], ['verified', 'printed'], true),
        ));
    }

    private function findRequest(int $id, ?int $campusId, ?string $type = null, bool $registrarOnly = false): array
    {
        $request = $this->requests->accessibleRequest($id, $campusId, $registrarOnly, $type);
        if ($request === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $request;
    }

    private function findLink(int $id): array
    {
        $link = $this->links->find($id);
        if ($link === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $link;
    }

    private function assertCampus(int $campusId): void
    {
        if (! $this->campuses->exists($campusId)) {
            throw new RuntimeException('Choose a valid campus.');
        }
    }

    private function linkData(array $data): array
    {
        $opensAt = strtotime((string) $data['opens_at']);
        $closesAt = strtotime((string) $data['closes_at']);
        if ($opensAt === false || $closesAt === false || $closesAt <= $opensAt) {
            throw new RuntimeException('Closing time must be after opening time.');
        }

        return [
            'campus_id' => (int) $data['campus_id'],
            'school_year' => trim((string) $data['school_year']),
            'opens_at' => date('Y-m-d H:i:s', $opensAt),
            'closes_at' => date('Y-m-d H:i:s', $closesAt),
            'release_date' => (string) $data['release_date'],
        ];
    }

    private function studentData(array $data): array
    {
        return [
            'student_no' => trim((string) $data['student_no']),
            'first_name' => trim((string) $data['first_name']),
            'last_name' => trim((string) $data['last_name']),
            'middle_name' => trim((string) $data['middle_name']) ?: null,
            'program_strand' => trim((string) $data['program_strand']),
            'year_level' => trim((string) $data['year_level']),
            'section' => trim((string) $data['section']),
            'contact_no' => trim((string) $data['contact_no']),
            'email' => trim((string) $data['email']) ?: null,
        ];
    }
}
