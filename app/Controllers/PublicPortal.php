<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PublicPortalService;
use RuntimeException;

final class PublicPortal extends BaseController
{
    public function __construct(private readonly PublicPortalService $portal = new PublicPortalService()) {}
    public function register(string $token): string { return view('public/register', $this->portal->registrationPage($token)); }

    public function storeRegistration(string $token)
    {
        $data = $this->request->getPost(); $rules = ['student_no'=>'required|max_length[50]','first_name'=>'required|max_length[80]','last_name'=>'required|max_length[80]','program_strand'=>'required|max_length[120]','year_level'=>'required|max_length[30]','section'=>'required|max_length[50]','contact_no'=>'required|max_length[30]','consent'=>'required'];
        if (! $this->validateData($data, $rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $photo = $this->request->getFile('photo'); $path = null;
        if ($photo !== null && $photo->isValid() && ! $photo->hasMoved()) { if (! in_array($photo->getMimeType(), ['image/jpeg','image/png'], true) || $photo->getSizeByUnit('mb') > 5) return redirect()->back()->withInput()->with('error', 'Photo must be a JPG or PNG under 5 MB.'); $path = $photo->store('private/id-photos'); }
        try { $reference = $this->portal->register($token, $data, $path); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage()); }
        return redirect()->to(site_url('id') . '?ref=' . $reference)->with('success', 'Submitted. Keep your reference number: ' . $reference);
    }

    public function track(): string
    {
        $ref = trim((string) $this->request->getGetPost('ref'));
        return view('public/track', ['ref'=>$ref] + ($ref === '' ? ['ticket'=>null,'history'=>[]] : $this->portal->trackingData($ref)));
    }

    public function resetRequest(): string { return view('public/reset'); }
    public function storeResetRequest()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['student_no'=>'required|max_length[50]','requester_name'=>'required|max_length[160]','contact'=>'required|max_length[160]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $reference = $this->portal->createResetRequest($data);
        return redirect()->to(site_url('id') . '?ref=' . $reference)->with('success', 'Request received. Identity verification is required before a reset. Reference: ' . $reference);
    }

    public function storeInstitutionalAccountRequest()
    {
        $data = $this->request->getPost();
        $rules = [
            'student_no' => 'required|max_length[50]',
            'first_name' => 'required|max_length[80]',
            'last_name' => 'required|max_length[80]',
            'middle_name' => 'permit_empty|max_length[80]',
            'campus_id' => 'required|integer',
            'program_strand' => 'required|max_length[120]',
            'year_level' => 'required|max_length[30]',
            'section' => 'required|max_length[50]',
            'contact_no' => 'required|max_length[30]',
            'email' => 'permit_empty|valid_email|max_length[160]',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url('id') . '?request=institutional-account')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        try {
            $reference = $this->portal->createInstitutionalAccountRequest($data);
        } catch (RuntimeException $exception) {
            return redirect()->to(site_url('id') . '?request=institutional-account')
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()->to(site_url('id') . '?request=track&ref=' . $reference)
            ->with('success', 'Institutional account request received. Keep your reference: ' . $reference);
    }

    public function storeReplacementRequest()
    {
        $data = $this->request->getPost();
        $rules = [
            'student_no' => 'required|max_length[50]',
            'campus_id' => 'required|integer',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url('id') . '?request=replacement')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        try {
            $reference = $this->portal->createReplacementRequest($data);
        } catch (RuntimeException $exception) {
            return redirect()->to(site_url('id') . '?request=replacement')
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()->to(site_url('id') . '?request=track&ref=' . $reference)
            ->with('success', 'Lost-ID replacement request received. Keep your reference: ' . $reference);
    }

    public function repairRequest(): string { return view('public/repair'); }
    public function storeRepairRequest()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['requester'=>'required|max_length[160]','contact'=>'required|max_length[160]','office'=>'required|max_length[160]','equipment'=>'required|max_length[160]','issue'=>'required|max_length[2000]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $reference = $this->portal->createRepairRequest($data);
        return redirect()->to('repair-request/' . $reference . '/print')->with('success', 'Request received.');
    }

    public function printRepair(string $ref): string { return view('public/repair_print', ['request'=>$this->portal->repairForPrint($ref)]); }
}
