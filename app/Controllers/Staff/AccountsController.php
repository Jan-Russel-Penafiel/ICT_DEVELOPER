<?php

declare(strict_types=1);

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Services\AccountManagementService;
use App\Services\ResetTicketService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

final class AccountsController extends BaseController
{
    public function __construct(
        private readonly AccountManagementService $accountService = new AccountManagementService(),
        private readonly ResetTicketService $resetService = new ResetTicketService(),
    ) {
    }

    public function index(): string { return view('staff/accounts', $this->accountService->indexData()); }

    public function createIndividual()
    {
        $data = $this->request->getPost();
        $rules = [
            'student_id' => 'required|integer',
            'org_unit' => 'required|max_length[190]',
            'default_password' => 'required|min_length[8]|max_length[255]',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'create-individual-account');
        }

        try {
            $csv = $this->accountService->createIndividual(
                (int) $data['student_id'],
                trim((string) $data['org_unit']),
                (string) $data['default_password'],
                auth()->id(),
            );
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'create-individual-account');
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="institutional-account.csv"')
            ->setBody($csv);
    }

    public function updateIndividual(int $id)
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['org_unit' => 'required|max_length[190]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'individual-edit-' . $id);
        }
        try {
            $this->accountService->updateIndividual($id, $data);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage())->with('open_modal', 'individual-edit-' . $id);
        } catch (PageNotFoundException $exception) {
            return redirect()->to('staff/accounts')->with('error', 'Individual account not found.');
        }
        return redirect()->to('staff/accounts')->with('success', 'Individual account updated.');
    }

    public function deleteIndividual(int $id)
    {
        try {
            $this->accountService->archiveIndividual($id);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        } catch (PageNotFoundException $exception) {
            return redirect()->back()->with('error', 'Individual account not found.');
        }
        return redirect()->to('staff/accounts')->with('success', 'Individual account archived.');
    }

    public function import()
    {
        $file = $this->request->getFile('file');
        if ($file === null) return redirect()->back()->with('error', 'Choose a CSV or XLSX file.');
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['campus_id' => 'required|integer', 'section' => 'required|max_length[50]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'import-accounts');
        }
        try { $added = $this->accountService->import($file, (int) $data['campus_id'], trim((string) $data['section']), auth()->id()); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'import-accounts'); }
        return redirect()->to('staff/accounts')->with('success', "Imported {$added} records. Temporary passwords are generated only during download.");
    }

    public function export(int $id)
    {
        $csv = $this->accountService->exportCsv($id);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="google-batch-' . $id . '.csv"')->setBody($csv);
    }

    public function downloadTemplate()
    {
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="institutional-account-import-template.xlsx"')
            ->setBody($this->accountService->importTemplate());
    }

    public function downloadCsvTemplate()
    {
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="institutional-account-import-template.csv"')
            ->setBody($this->accountService->importCsvTemplate());
    }

    public function downloadSource(int $id)
    {
        $source = $this->accountService->sourceFilePath($id);
        return $this->response
            ->setHeader('Content-Type', mime_content_type($source['path']) ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . str_replace('"', '', $source['name']) . '"')
            ->setBody((string) file_get_contents($source['path']));
    }

    public function updateBatch(int $id)
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['campus_id'=>'required|integer','section'=>'required|max_length[50]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'batch-edit-' . $id);
        try { $this->accountService->updateBatch($id, $data); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'batch-edit-' . $id); }
        return redirect()->to('staff/accounts')->with('success', 'Account batch updated.');
    }

    public function changeBatchStatus(int $id)
    {
        try {
            $this->accountService->changeBatchStatus($id, (string) $this->request->getPost('status'));
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        } catch (\DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('staff/accounts')->with('success', 'Account batch status updated.');
    }

    public function deleteBatch(int $id)
    {
        try { $this->accountService->archiveBatch($id); } catch (RuntimeException $exception) { return redirect()->back()->with('error', $exception->getMessage()); }
        return redirect()->to('staff/accounts')->with('success', 'Account batch and its accounts archived.');
    }

    public function resets(): string { return view('staff/resets', ['requests' => $this->resetService->all()]); }

    public function updateReset(int $id)
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['student_no'=>'required|max_length[50]','requester_name'=>'required|max_length[160]','contact'=>'required|max_length[160]','channel'=>'required|in_list[online,walk-in,email,phone]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'reset-edit-' . $id);
        $this->resetService->update($id, $data);
        return redirect()->to('staff/resets')->with('success', 'Reset ticket details updated.');
    }

    public function deleteReset(int $id) { $this->resetService->archive($id); return redirect()->to('staff/resets')->with('success', 'Reset ticket archived.'); }

    public function changeReset(int $id)
    {
        try { $this->resetService->transition($id, (string) $this->request->getPost('status'), auth()->id()); } catch (RuntimeException $exception) { return redirect()->back()->with('error', $exception->getMessage()); }
        return redirect()->back()->with('success', 'Reset ticket updated.');
    }
}
