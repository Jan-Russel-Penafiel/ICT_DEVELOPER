<?php

declare(strict_types=1);

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Services\StaffAccountService;
use CodeIgniter\Shield\Authentication\Passwords;
use RuntimeException;

final class StaffAccountsController extends BaseController
{
    public function __construct(private readonly StaffAccountService $accounts = new StaffAccountService()) {}
    public function index(): string { return view('staff/staff_accounts', $this->accounts->indexData()); }

    public function create()
    {
        $data = $this->accountData();
        if (! $this->validateData($data, $this->rules(true), [], config('Auth')->DBGroup)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        try { $this->accounts->create($data); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage()); }
        return redirect()->to('staff/users')->with('success', 'Staff account created. Provide the sign-in details through an approved channel.');
    }

    public function update(int $id)
    {
        if ($id === (int) auth()->id()) return redirect()->back()->with('error', 'Use a separate administrator account to change your own access.');
        $data = $this->accountData();
        if (! $this->validateData($data, $this->rules(false), [], config('Auth')->DBGroup)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('open_modal', 'user-edit-' . $id);
        try { $this->accounts->update($id, $data); } catch (RuntimeException $exception) { return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('open_modal', 'user-edit-' . $id); }
        return redirect()->to('staff/users')->with('success', 'Staff account updated.');
    }

    public function delete(int $id)
    {
        if ($id === (int) auth()->id()) return redirect()->back()->with('error', 'You cannot archive your own signed-in account.');
        try { $this->accounts->archive($id); } catch (RuntimeException $exception) { return redirect()->back()->with('error', $exception->getMessage()); }
        return redirect()->to('staff/users')->with('success', 'Staff account archived.');
    }

    private function accountData(): array
    {
        return ['username'=>trim((string) $this->request->getPost('username')), 'email'=>strtolower(trim((string) $this->request->getPost('email'))), 'password'=>(string) $this->request->getPost('password'), 'password_confirm'=>(string) $this->request->getPost('password_confirm'), 'group'=>(string) $this->request->getPost('group'), 'campus_id'=>(string) $this->request->getPost('campus_id'), 'active'=>(string) $this->request->getPost('active')];
    }

    private function rules(bool $creating): array
    {
        $tables = config('Auth')->tables; $groups = implode(',', array_keys(config('AuthGroups')->groups));
        return [
            'username' => $creating ? ['rules'=>['required','min_length[3]','max_length[30]','regex_match[/\\A[a-zA-Z0-9\\.]+\\z/]','is_unique[' . $tables['users'] . '.username]']] : 'required|min_length[3]|max_length[30]|regex_match[/\\A[a-zA-Z0-9\\.]+\\z/]',
            'email' => $creating ? ['rules'=>['required','max_length[254]','valid_email','is_unique[' . $tables['identities'] . '.secret]']] : 'required|max_length[254]|valid_email',
            'password' => $creating ? ['rules'=>['required', Passwords::getMaxLengthRule()]] : 'permit_empty|' . Passwords::getMaxLengthRule(),
            'password_confirm' => $creating ? 'required|matches[password]' : 'matches[password]',
            'group' => 'required|in_list[' . $groups . ']', 'campus_id' => 'permit_empty|is_natural_no_zero', 'active' => $creating ? 'permit_empty' : 'required|in_list[0,1]',
        ];
    }
}
