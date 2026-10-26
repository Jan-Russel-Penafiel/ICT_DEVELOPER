<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountBatchModel;
use App\Models\AccountModel;
use App\Models\CampusModel;
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
    ) {
    }

    public function indexData(): array
    {
        return [
            'batches' => $this->batches->withAccountCounts(),
            'individualAccounts' => $this->accounts->individualWithDetails(),
            'campuses' => $this->campuses->alphabetical(),
            'sectionsByCampus' => $this->students->sectionsByCampus(),
            'students' => $this->students
                ->select('id, student_no, first_name, middle_name, last_name, campus_id, section')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->findAll(),
        ];
    }

    public function createIndividual(int $studentId, string $orgUnit, string $defaultPassword, ?int $userId): string
    {
        $student = $this->students->find($studentId);
        if (! $student) throw new RuntimeException('Choose a valid student.');
        if ($orgUnit === '') throw new RuntimeException('Enter an organizational unit.');

        $domain = ltrim((string) ($this->settings->find('email_domain')['value'] ?? ''), '@');
        if ($domain === '') throw new RuntimeException('Configure the institutional email domain before creating an account.');
        $email = $this->buildStudentEmail($student, $domain);
        if ($this->accounts->where('email', $email)->first()) {
            throw new RuntimeException('This student already has an institutional account.');
        }

        $db = db_connect();
        $db->transStart();
        $this->accounts->insert([
            'account_batch_id' => null,
            'student_id' => $studentId,
            'email' => $email,
            'org_unit' => $orgUnit,
        ]);
        $db->transComplete();
        if (! $db->transStatus()) throw new RuntimeException('The individual account could not be created.');

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['First Name', 'Last Name', 'Email Address', 'Password', 'Org Unit Path', 'Change Password at Next Sign-In']);
        fputcsv($output, [$student['first_name'], $student['last_name'], $email, $defaultPassword, $orgUnit, 'TRUE']);
        rewind($output);
        $csv = (string) stream_get_contents($output);
        fclose($output);

        return $csv;
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
            ->whereIn('email', array_map(function (string $studentNo) use ($studentsByNumber, $domain): string { return $this->buildStudentEmail($studentsByNumber[$studentNo], $domain); }, array_keys($studentNumbers)))
            ->findAll();
        if ($existingEmails !== []) throw new RuntimeException('One or more students in this section already have an institutional account.');

        $db = db_connect(); $db->transStart();
        $sourcePath = $file->getRandomName();
        $batchId = $this->batches->insert(['campus_id' => $campusId, 'section' => $section, 'source_file' => $file->getClientName(), 'source_path' => 'account-batches/' . $sourcePath, 'created_by' => $userId], true);
        $added = 0;
        foreach (array_keys($studentNumbers) as $studentNo) {
            $student = $studentsByNumber[$studentNo];
            $email = $this->buildStudentEmail($student, $domain);
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

    private function buildStudentEmail(array $student, string $domain): string
    {
        $localPart = strtolower(trim((string) ($student['first_name'] ?? '')) . ' ' . trim((string) ($student['last_name'] ?? '')));
        $localPart = str_replace(['.', ' '], '', $localPart);
        return strtolower($localPart . '@' . $domain);
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
            ['student_no', 'First Name', 'Last Name', 'Domain Extension', 'Default Password', 'Organizational Unit'],
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

    public function importCsvTemplate(): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['student_no', 'First Name', 'Last Name', 'Domain Extension', 'Default Password', 'Organizational Unit']);
        fputcsv($output, ['2026-00001']);
        fputcsv($output, ['2026-00002']);
        fputcsv($output, ['2026-00003']);
        rewind($output);
        $contents = (string) stream_get_contents($output);
        fclose($output);

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

    public function updateIndividual(int $id, array $data): void
    {
        $account = $this->accounts->findIndividual($id);
        if (! $account) throw PageNotFoundException::forPageNotFound();

        $orgUnit = trim((string) ($data['org_unit'] ?? ''));
        if ($orgUnit === '') throw new RuntimeException('Enter an organizational unit.');

        $this->accounts->update($id, ['org_unit' => $orgUnit]);
    }

    public function archiveIndividual(int $id): void
    {
        $account = $this->accounts->findIndividual($id);
        if (! $account) throw PageNotFoundException::forPageNotFound();

        if (! $this->accounts->delete($id)) {
            throw new RuntimeException('The individual account could not be archived.');
        }
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

    public function archiveBatch(int $id): void
    {
        $batch = $this->batches->find($id);
        if (! $batch) throw PageNotFoundException::forPageNotFound();
        $db = db_connect(); $db->transStart(); $this->accounts->deleteForBatch($id); $this->batches->delete($id); $db->transComplete();
        if (! $db->transStatus()) throw new RuntimeException('The account batch could not be archived.');
        if (! empty($batch['source_path']) && is_file(WRITEPATH . 'uploads/' . $batch['source_path'])) unlink(WRITEPATH . 'uploads/' . $batch['source_path']);
    }
}
