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
