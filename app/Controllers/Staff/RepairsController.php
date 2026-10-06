<?php

declare(strict_types=1);

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Services\RepairRequestService;
use RuntimeException;

final class RepairsController extends BaseController
{
    public function __construct(private readonly RepairRequestService $repairs = new RepairRequestService()) {}

    public function index(): string { return view('staff/repairs', $this->repairs->indexData()); }

    public function update(int $id)
    {
        $data = $this->request->getPost();
        $rules = ['requester'=>'required|max_length[160]','contact'=>'required|max_length[160]','office'=>'required|max_length[160]','equipment'=>'required|max_length[160]','serial_no'=>'permit_empty|max_length[100]','issue'=>'required|max_length[2000]'];
        if (! $this->validateData($data, $rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'repair-edit-' . $id);
        $this->repairs->update($id, $data);
        return redirect()->to('staff/repairs')->with('success', 'Repair request details updated.');
    }

    public function delete(int $id) { $this->repairs->archive($id); return redirect()->to('staff/repairs')->with('success', 'Repair request archived.'); }

    public function changeStatus(int $id)
    {
        try { $this->repairs->transition($id, (string) $this->request->getPost('status'), $this->request->getPost()); } catch (RuntimeException $exception) { return redirect()->back()->with('error', $exception->getMessage()); }
        return redirect()->back()->with('success', 'Repair request updated.');
    }
}
