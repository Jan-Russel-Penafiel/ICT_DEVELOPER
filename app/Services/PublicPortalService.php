<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\IdRequestModel;
use App\Models\InstitutionalAccountRequestModel;
use App\Models\CampusModel;
use App\Models\RegistrationLinkModel;
use App\Models\RepairRequestModel;
use App\Models\ResetRequestModel;
use App\Models\StatusLogModel;
use App\Models\StudentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

final class PublicPortalService
{
    public function __construct(
        private readonly RegistrationLinkModel $links = new RegistrationLinkModel(),
        private readonly CampusModel $campuses = new CampusModel(),
        private readonly StudentModel $students = new StudentModel(),
        private readonly InstitutionalAccountRequestModel $institutionalAccountRequests = new InstitutionalAccountRequestModel(),
        private readonly IdRequestModel $idRequests = new IdRequestModel(),
        private readonly RepairRequestModel $repairs = new RepairRequestModel(),
        private readonly ResetRequestModel $resets = new ResetRequestModel(),
        private readonly StatusLogModel $statusLogs = new StatusLogModel(),
    ) {}

    public function institutionalAccountRequestPage(): array
    {
        return ['campuses' => $this->campuses->alphabetical()];
    }

    public function createInstitutionalAccountRequest(array $data): string
    {
        $campusId = (int) $data['campus_id'];
        if (! $this->campuses->exists($campusId)) {
            throw new RuntimeException('Choose a valid campus.');
        }

        $studentNo = trim((string) $data['student_no']);
        if ($this->institutionalAccountRequests
            ->where('student_no', $studentNo)
            ->where('campus_id', $campusId)
            ->whereIn('status', ['submitted', 'processing'])
            ->first() !== null) {
            throw new RuntimeException('An individual institutional account request already exists for this student and campus.');
        }

        $reference = 'ACC-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $requestId = $this->institutionalAccountRequests->insert([
            'ref_no' => $reference,
            'student_no' => $studentNo,
            'first_name' => trim((string) $data['first_name']),
            'last_name' => trim((string) $data['last_name']),
            'middle_name' => trim((string) ($data['middle_name'] ?? '')) ?: null,
            'campus_id' => $campusId,
            'program_strand' => trim((string) $data['program_strand']),
            'year_level' => trim((string) $data['year_level']),
            'section' => trim((string) $data['section']),
            'contact_no' => trim((string) $data['contact_no']),
            'email' => trim((string) ($data['email'] ?? '')) ?: null,
            'status' => 'submitted',
        ], true);
        $this->statusLogs->record('institutional_account_request', (int) $requestId, 'submitted');

        return $reference;
    }

    public function createReplacementRequest(array $data): string
    {
        $studentNo = trim((string) $data['student_no']);
        $campusId = (int) $data['campus_id'];
        $student = $this->students->findByStudentNumber($studentNo, $campusId);
        if ($student === null) {
            throw new RuntimeException('The student number was not found for the selected campus.');
        }
        if ($this->idRequests->where('student_id', $student['id'])
            ->where('type', 'replacement')
            ->whereIn('status', ['submitted', 'processing', 'paid', 'verified', 'printed'])
            ->first() !== null) {
            throw new RuntimeException('An active Lost-ID replacement request already exists for this student.');
        }

        $reference = 'IDR-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $requestId = $this->idRequests->insert([
            'student_id' => $student['id'],
            'reference_no' => $reference,
            'type' => 'replacement',
            'status' => 'submitted',
        ], true);
        $this->statusLogs->record('id_request', (int) $requestId, 'submitted');

        return $reference;
    }

    public function registrationPage(string $token): array
    {
        $link = $this->links->findWithCampusByToken($token);
        if ($link === null) throw PageNotFoundException::forPageNotFound();
        return ['link' => $link, 'open' => $link['is_active'] && time() >= strtotime($link['opens_at']) && time() <= strtotime($link['closes_at'])];
    }

    public function register(string $token, array $data, ?string $photoPath): string
    {
        $link = $this->links->findOpenByToken($token);
        if ($link === null) throw new RuntimeException('This registration link is not open.');
        $studentNo = trim((string) $data['student_no']);
        if ($this->students->studentNumberExistsForCampus($studentNo, (int) $link['campus_id'])) throw new RuntimeException('This student number already has a request for this campus.');
        $now = date('Y-m-d H:i:s'); $reference = 'ID-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $db = db_connect(); $db->transStart();
        $studentId = $this->students->insert(['student_no'=>$studentNo,'first_name'=>trim((string) $data['first_name']),'last_name'=>trim((string) $data['last_name']),'middle_name'=>trim((string) $data['middle_name']) ?: null,'campus_id'=>$link['campus_id'],'program_strand'=>trim((string) $data['program_strand']),'year_level'=>trim((string) $data['year_level']),'section'=>trim((string) $data['section']),'contact_no'=>trim((string) $data['contact_no']),'email'=>trim((string) $data['email']) ?: null,'photo_path'=>$photoPath,'consent_at'=>$now], true);
        $requestId = $this->idRequests->insert(['student_id'=>$studentId,'registration_link_id'=>$link['id'],'reference_no'=>$reference,'type'=>'new','status'=>'submitted'], true);
        $this->statusLogs->record('id_request', (int) $requestId, 'submitted'); $db->transComplete();
        if (! $db->transStatus()) throw new RuntimeException('Your registration could not be submitted. Please try again.');
        return $reference;
    }

    public function trackingData(string $reference): array
    {
        $ticket = $this->idRequests->findForTracking($reference); $entityType = 'id_request';
        if ($ticket === null) { $ticket = $this->repairs->findForTracking($reference); $entityType = 'repair_request'; }
        if ($ticket === null) { $ticket = $this->resets->findForTracking($reference); $entityType = 'reset_request'; }
        if ($ticket === null) { $ticket = $this->institutionalAccountRequests->findForTracking($reference); $entityType = 'institutional_account_request'; }
        return ['ticket' => $ticket, 'history' => $ticket === null ? [] : $this->statusLogs->forEntity($entityType, (int) $ticket['id'])];
    }

    public function createResetRequest(array $data): string
    {
        $studentNo = trim((string) $data['student_no']); $student = $this->students->findByStudentNumber($studentNo); $reference = 'RST-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $requestId = $this->resets->insert(['ref_no'=>$reference,'student_id'=>$student['id'] ?? null,'student_no'=>$studentNo,'requester_name'=>trim((string) $data['requester_name']),'contact'=>trim((string) $data['contact']),'channel'=>'online','status'=>'requested'], true);
        $this->statusLogs->record('reset_request', (int) $requestId, 'requested');
        return $reference;
    }

    public function createRepairRequest(array $data): string
    {
        $reference = 'RPR-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $requestId = $this->repairs->insert(['request_no'=>$reference,'requester'=>trim((string) $data['requester']),'contact'=>trim((string) $data['contact']),'office'=>trim((string) $data['office']),'equipment'=>trim((string) $data['equipment']),'serial_no'=>trim((string) ($data['serial_no'] ?? '')) ?: null,'issue'=>trim((string) $data['issue']),'status'=>'received'], true);
        $this->statusLogs->record('repair_request', (int) $requestId, 'received');
        return $reference;
    }

    public function repairForPrint(string $reference): array
    {
        $request = $this->repairs->findByRequestNumber($reference);
        if ($request === null) throw PageNotFoundException::forPageNotFound();
        return $request;
    }
}
