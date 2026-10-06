<?php

declare(strict_types=1);

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Services\IdManagementService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use RuntimeException;

final class IdController extends BaseController
{
    public function __construct(private readonly IdManagementService $ids = new IdManagementService()) {}

    public function index(): string { return view('staff/ids', $this->ids->queueData(staff_campus_id(), $this->registrarOnly())); }
    public function links(): string { return view('staff/links', $this->ids->linksData()); }

    public function createLink()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['campus_id'=>'required|integer','school_year'=>'required|max_length[20]','opens_at'=>'required','closes_at'=>'required','release_date'=>'required'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        try { $this->ids->createLink($data, auth()->id()); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage()); }
        return redirect()->to('staff/links')->with('success', 'Registration link created.');
    }

    public function updateLink(int $id)
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['campus_id'=>'required|integer','school_year'=>'required|max_length[20]','opens_at'=>'required','closes_at'=>'required','release_date'=>'required','is_active'=>'required|in_list[0,1]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'link-edit-' . $id);
        try { $this->ids->updateLink($id, $data); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'link-edit-' . $id); }
        return redirect()->to('staff/links')->with('success', 'Registration link updated.');
    }

    public function deleteLink(int $id) { $this->ids->archiveLink($id); return redirect()->to('staff/links')->with('success', 'Registration link archived.'); }

    public function qr(int $id)
    {
        $link = $this->ids->linkForQr($id, staff_campus_id());
        $result = (new PngWriter())->write(new QrCode(data: site_url('register/' . $link['token']), size: 600, margin: 20));
        return $this->response->setHeader('Content-Type', 'image/png')->setHeader('Content-Disposition', 'inline; filename="registration-qr-' . $id . '.png"')->setBody($result->getString());
    }

    public function updateRequest(int $id)
    {
        $data = $this->request->getPost();
        $rules = ['student_no'=>'required|max_length[50]','first_name'=>'required|max_length[80]','last_name'=>'required|max_length[80]','middle_name'=>'permit_empty|max_length[80]','program_strand'=>'required|max_length[120]','year_level'=>'required|max_length[30]','section'=>'required|max_length[50]','contact_no'=>'required|max_length[30]','email'=>'permit_empty|valid_email|max_length[160]'];
        if (! $this->validateData($data, $rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'id-edit-' . $id);
        try { $this->ids->updateRequest($id, $data, staff_campus_id()); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'id-edit-' . $id); }
        return redirect()->to('staff/ids')->with('success', 'ID request details updated.');
    }

    public function updateReplacement(int $id)
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['or_number'=>'required|max_length[60]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'replacement-edit-' . $id);
        $this->ids->updateReplacement($id, (string) $data['or_number'], staff_campus_id());
        return redirect()->to('staff/replacements')->with('success', 'Replacement payment details updated.');
    }

    public function deleteRequest(int $id) { $this->ids->archiveRequest($id, staff_campus_id()); return redirect()->back()->with('success', 'ID request archived.'); }

    public function changeStatus(int $id)
    {
        try { $this->ids->changeStatus($id, (string) $this->request->getPost('status'), (string) $this->request->getPost('received_by'), staff_campus_id(), $this->registrarOnly(), auth()->id()); } catch (RuntimeException $exception) { return redirect()->back()->with('error', $exception->getMessage()); }
        return redirect()->back()->with('success', 'ID request updated.');
    }

    public function export()
    {
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->fromArray(['Reference', 'Student No', 'Name', 'Campus', 'Program', 'Section', 'Status'], null, 'A1');
        foreach ($this->ids->productionRows(staff_campus_id()) as $row) $sheet->fromArray([$row['reference_no'], $row['student_no'], $row['last_name'] . ', ' . $row['first_name'], $row['campus'], $row['program_strand'], $row['section'], $row['status']], null, 'A' . ($sheet->getHighestRow() + 1));
        $writer = new Csv($sheet->getParent()); ob_start(); $writer->save('php://output'); $csv = (string) ob_get_clean();
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="id-production.csv"')->setBody($csv);
    }

    public function replacements(): string { return view('staff/replacements', $this->ids->replacementsData(staff_campus_id())); }

    public function recordReplacement()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['student_no'=>'required|max_length[50]','or_number'=>'required|max_length[60]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        try { $this->ids->recordReplacement($data, staff_campus_id(), auth()->id()); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage()); }
        return redirect()->to('staff/replacements')->with('success', 'Payment recorded; replacement is now in ICT queue.');
    }

    private function registrarOnly(): bool { return staff_can('registrar') && ! staff_can('admin', 'ict'); }
}
