<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountBatchModel;
use App\Models\AccountModel;
use App\Models\CampusModel;
use App\Models\InstitutionalAccountRequestModel;
use App\Models\SettingModel;
use App\Models\StudentModel;
use App\Services\StatusTransitionService;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Exceptions\PageNotFoundException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

final class AccountManagementService
{
    public function __construct(
        private readonly AccountBatchModel $batches = new AccountBatchModel(),
        private readonly AccountModel $accounts = new AccountModel(),
        private readonly CampusModel $campuses = new CampusModel(),
        private readonly StudentModel $students = new StudentModel(),
        private readonly SettingModel $settings = new SettingModel(),
        private readonly InstitutionalAccountRequestModel $individualRequests = new InstitutionalAccountRequestModel(),
    ) {
    }

    public function indexData(): array
    {
        return [
            'batches' => $this->batches->withAccountCounts(),
            'individualRequests' => $this->individualRequests->allWithCampus(),
            'campuses' => $this->campuses->alphabetical(),
            'sectionsByCampus' => $this->students->sectionsByCampus(),
        ];
    }

    public function import(UploadedFile $file, int $campusId, string $section, ?int $userId): int
    {
        if (! $this->campuses->find($campusId)) throw new RuntimeException('Choose a valid campus.');
        $section = trim($section);
        if ($section === '') throw new RuntimeException('Choose the section for this student list.');
        if (! $file->isValid() || $file->getSizeByUnit('mb') > 10 || ! in_array($file->getClientExtension(), ['csv', 'xlsx'], true)) throw new RuntimeException('Choose a valid CSV or XLSX file under 10 MB.');
        try { $rows = IOFactory::load($file->getTempName())->getActiveSheet()->toArray(null, true, true, true); } catch (\Throwable) { throw new RuntimeException('The uploaded file could not be read.'); }
        if (count($rows) < 2) throw new RuntimeException('The file does not contain records.');
        $studentKey = $this->studentNumberColumn(array_shift($rows));
        $studentNumbers = [];
        foreach ($rows as $row) {
            $studentNo = trim((string) ($row[$studentKey] ?? ''));
            if ($studentNo === '') continue;
            if (isset($studentNumbers[$studentNo])) throw new RuntimeException("The student number {$studentNo} appears more than once.");
            $studentNumbers[$studentNo] = true;
        }
        if ($studentNumbers === []) throw new RuntimeException('The file does not contain student numbers.');
        if (count($studentNumbers) > 500) throw new RuntimeException('A section roster can contain up to 500 students.');

        $students = $this->students
            ->where('campus_id', $campusId)
            ->whereIn('student_no', array_keys($studentNumbers))
            ->findAll();
        $selectedSection = $this->normalizeSection($section);
        $studentsByNumber = [];
        foreach ($students as $student) {
            if ($this->normalizeSection((string) $student['section']) === $selectedSection) {
                $studentsByNumber[(string) $student['student_no']] = $student;
            }
        }
        $missingStudents = array_diff(array_keys($studentNumbers), array_keys($studentsByNumber));
        if ($missingStudents !== []) throw new RuntimeException('Every student number must belong to the selected campus and section.');

        $domain = $this->settings->find('email_domain')['value'] ?? 'example.edu.ph';
        $existingEmails = $this->accounts
            ->whereIn('email', array_map(static fn (string $studentNo): string => strtolower($studentNo . '@' . ltrim($domain, '@')), array_keys($studentNumbers)))
            ->findAll();
        if ($existingEmails !== []) throw new RuntimeException('One or more students in this section already have an institutional account.');

        $db = db_connect(); $db->transStart();
        $sourcePath = $file->getRandomName();
        $batchId = $this->batches->insert(['campus_id' => $campusId, 'section' => $section, 'source_file' => $file->getClientName(), 'source_path' => 'account-batches/' . $sourcePath, 'created_by' => $userId], true);
        $added = 0;
        foreach (array_keys($studentNumbers) as $studentNo) {
            $student = $studentsByNumber[$studentNo];
            $email = strtolower($studentNo . '@' . ltrim($domain, '@'));
            $this->accounts->insert(['account_batch_id' => $batchId, 'student_id' => $student['id'], 'email' => $email, 'org_unit' => '/Students/' . $campusId]);
            $added++;
        }
        $db->transComplete();
        if (! $db->transStatus()) throw new RuntimeException('The account list could not be imported.');
        $uploadPath = WRITEPATH . 'uploads/account-batches';
        if (! is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
        $file->move($uploadPath, $sourcePath);
        return $added;
    }

    /** @param array<int|string, mixed> $headers */
    private function studentNumberColumn(array $headers): int|string
    {
        foreach ($headers as $column => $header) {
            $normalizedHeader = strtolower(trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $header)));
            if ($normalizedHeader === 'student_no') return $column;
        }

        throw new RuntimeException('The header must include student_no.');
    }

    private function normalizeSection(string $section): string
    {
        return strtolower(trim((string) preg_replace('/\s+/', ' ', $section)));
    }

    public function sourceFilePath(int $id): array
    {
        $batch = $this->batches->find($id);
        if (! $batch || empty($batch['source_path'])) throw PageNotFoundException::forPageNotFound();
        $path = WRITEPATH . 'uploads/' . $batch['source_path'];
        if (! is_file($path)) throw PageNotFoundException::forPageNotFound();
        return ['path' => $path, 'name' => $batch['source_file'] ?: basename($path)];
    }

    public function exportCsv(int $id): string
    {
        if (! $this->batches->find($id)) throw PageNotFoundException::forPageNotFound();
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['First Name', 'Last Name', 'Email Address', 'Password', 'Org Unit Path', 'Change Password at Next Sign-In']);
        foreach ($this->accounts->forBatchExport($id) as $account) fputcsv($output, [$account['first_name'], $account['last_name'], $account['email'], bin2hex(random_bytes(8)), $account['org_unit'], 'TRUE']);
        rewind($output); $csv = (string) stream_get_contents($output); fclose($output);
        $this->batches->update($id, ['exported_at' => date('Y-m-d H:i:s')]);
        return $csv;
    }

    public function importTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Account roster');
        $worksheet->fromArray([
            ['student_no'],
            ['2026-00001'],
            ['2026-00002'],
            ['2026-00003'],
        ]);
        $worksheet->getStyle('A1')->getFont()->setBold(true);
        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $contents = (string) ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return $contents;
    }

    public function updateBatch(int $id, array $data): void
    {
        if (! $this->batches->find($id)) throw PageNotFoundException::forPageNotFound();
        if (! $this->campuses->find((int) $data['campus_id'])) throw new RuntimeException('Choose a valid campus.');
        $section = trim((string) $data['section']);
        if ($section === '') throw new RuntimeException('Choose the section for this account batch.');

        $accountCount = $this->accounts->where('account_batch_id', $id)->countAllResults();
        $matchingStudentCount = db_connect()->table('accounts')
            ->join('students', 'students.id = accounts.student_id')
            ->where('accounts.account_batch_id', $id)
            ->where('students.campus_id', (int) $data['campus_id'])
            ->where('students.section', $section)
            ->countAllResults();
        if ($accountCount !== $matchingStudentCount) throw new RuntimeException('The campus and section must match every student in this account batch.');

        $this->batches->update($id, ['campus_id' => (int) $data['campus_id'], 'section' => $section]);
    }

    public function changeBatchStatus(int $id, string $status): void
    {
        if (! $this->batches->find($id)) throw PageNotFoundException::forPageNotFound();

        (new StatusTransitionService(db_connect()))->transition(
            'account_batches',
            $id,
            $status,
            ['active', 'inactive'],
            'account_batch',
        );
    }

    public function changeIndividualStatus(int $id, string $status): void
    {
        if (! $this->individualRequests->find($id)) throw PageNotFoundException::forPageNotFound();

        (new StatusTransitionService(db_connect()))->transition(
            'institutional_account_requests',
            $id,
            $status,
            ['processing', 'completed', 'rejected'],
            'institutional_account_request',
        );
    }

    public function archiveBatch(int $id): void
    {
        $batch = $this->batches->find($id);
        if (! $batch) throw PageNotFoundException::forPageNotFound();
        $db = db_connect(); $db->transStart(); $this->accounts->deleteForBatch($id); $this->batches->delete($id); $db->transComplete();
        if (! $db->transStatus()) throw new RuntimeException('The account batch could not be archived.');
        if (! empty($batch['source_path']) && is_file(WRITEPATH . 'uploads/' . $batch['source_path'])) unlink(WRITEPATH . 'uploads/' . $batch['source_path']);
    }
}
