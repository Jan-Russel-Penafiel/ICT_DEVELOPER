<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CampusModel;
use App\Models\StaffProfileModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use RuntimeException;
use Throwable;

final class StaffAccountService
{
    /** @var list<string> */
    private const CAMPUS_SCOPED_GROUPS = ['registrar', 'cashier'];

    public function __construct(
        private readonly CampusModel $campuses = new CampusModel(),
        private readonly StaffProfileModel $profiles = new StaffProfileModel(),
    ) {
    }

    public function indexData(): array
    {
        $authConfig = config('Auth');
        $tables = $authConfig->tables;
        $users = db_connect()->table($tables['users'] . ' u')
            ->select('u.id, u.username, u.active, i.secret AS email, GROUP_CONCAT(DISTINCT gu.group SEPARATOR ", ") AS groups, c.name AS campus, sp.campus_id')
            ->join($tables['identities'] . ' i', "i.user_id = u.id AND i.type = 'email_password'", 'left')
            ->join($tables['groups_users'] . ' gu', 'gu.user_id = u.id', 'left')
            ->join('staff_profiles sp', 'sp.user_id = u.id', 'left')
            ->join('campuses c', 'c.id = sp.campus_id', 'left')
            ->where('u.deleted_at', null)
            ->groupBy(['u.id', 'u.username', 'u.active', 'i.secret', 'c.name', 'sp.campus_id'])
            ->orderBy('u.username')
            ->get()
            ->getResultArray();

        return ['users' => $users, 'campuses' => $this->campuses->alphabetical(), 'groups' => config('AuthGroups')->groups];
    }

    public function create(array $data): void
    {
        $campusId = $this->campusAssignment($data['group'], $data['campus_id']);
        $user = new User(['username' => $data['username'], 'email' => $data['email'], 'password' => $data['password'], 'active' => true]);
        $this->assertPassword($data['password'], $user);

        $db = db_connect();
        $db->transBegin();
        try {
            $users = $this->userModel();
            $users->save($user);
            $createdUser = $users->findById($users->getInsertID());
            if ($createdUser === null) {
                throw new RuntimeException('The new staff user could not be loaded.');
            }
            $createdUser->addGroup($data['group']);
            $this->profiles->replaceCampusAssignment((int) $createdUser->id, $campusId);
            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Staff account creation failed: {message}', ['message' => $exception->getMessage()]);
            throw new RuntimeException('The staff account could not be created. Please try again.');
        }
    }

    public function update(int $id, array $data): void
    {
        $user = $this->findUser($id);
        if ($id === (int) (auth()->id() ?? 0)) {
            $currentGroups = $user->getGroups() ?? [];
            if (! in_array($data['group'], $currentGroups, true)) {
                throw new RuntimeException('Use a separate administrator account to change your own access.');
            }
            if ((bool) (int) $data['active'] !== (bool) $user->active) {
                throw new RuntimeException('Use a separate administrator account to change your own access.');
            }
        }
        $this->assertUniqueCredentials($id, $data['username'], $data['email']);
        $campusId = $this->campusAssignment($data['group'], $data['campus_id']);
        if ($data['password'] !== '') {
            $this->assertPassword($data['password'], $user);
            $user->password = $data['password'];
        }
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->active = (bool) (int) $data['active'];

        $db = db_connect();
        $db->transBegin();
        try {
            $this->userModel()->save($user);
            $user->syncGroups($data['group']);
            $this->profiles->replaceCampusAssignment($id, $campusId);
            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Staff account update failed: {message}', ['message' => $exception->getMessage()]);
            throw new RuntimeException('The staff account could not be updated.');
        }
    }

    public function archive(int $id): void
    {
        $user = $this->findUser($id);
        $db = db_connect();
        $db->transBegin();
        try {
            $this->profiles->replaceCampusAssignment($id, null);
            $this->userModel()->delete($user->id);
            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Staff account archive failed: {message}', ['message' => $exception->getMessage()]);
            throw new RuntimeException('The staff account could not be archived.');
        }
    }

    private function campusAssignment(string $group, string $campusValue): ?int
    {
        $campusId = $campusValue === '' ? null : (int) $campusValue;
        if (in_array($group, self::CAMPUS_SCOPED_GROUPS, true) && $campusId === null) {
            throw new RuntimeException('Registrar and Cashier accounts require a campus assignment.');
        }
        if ($campusId !== null && ! $this->campuses->exists($campusId)) {
            throw new RuntimeException('Choose a valid campus assignment.');
        }

        return $campusId;
    }

    private function assertPassword(string $password, User $user): void
    {
        $result = service('passwords')->check($password, $user);
        if (! $result->isOK()) {
            throw new RuntimeException($result->reason() ?? 'Choose a stronger password.');
        }
    }

    private function assertUniqueCredentials(int $id, string $username, string $email): void
    {
        $tables = config('Auth')->tables;
        $db = db_connect();
        $duplicateUsername = $db->table($tables['users'])->where('username', $username)->where('id !=', $id)->where('deleted_at', null)->countAllResults();
        $duplicateEmail = $db->table($tables['identities'])->where('type', 'email_password')->where('secret', $email)->where('user_id !=', $id)->countAllResults();
        if ($duplicateUsername || $duplicateEmail) {
            throw new RuntimeException('The username or email address is already in use.');
        }
    }

    private function findUser(int $id): User
    {
        $user = $this->userModel()->findById($id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $user;
    }

    /** @return UserModel */
    private function userModel(): UserModel
    {
        return model(setting('Auth.userProvider'));
    }
}
