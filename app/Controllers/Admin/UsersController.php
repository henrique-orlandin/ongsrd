<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User as ShieldUser;
use CodeIgniter\Shield\Models\GroupModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Validation\ValidationRules;

class UsersController extends AdminBaseController
{
    public function index()
    {
        $tables = config('Auth')->tables;

        $rows = model(UserModel::class)
            ->select($tables['users'] . '.id as id, username, active, last_active, ' . $tables['identities'] . '.secret as email')
            ->join($tables['identities'], $tables['users'] . '.id = ' . $tables['identities'] . '.user_id', 'left')
            ->groupStart()
                ->where($tables['identities'] . '.type', Session::ID_TYPE_EMAIL_PASSWORD)
                ->orGroupStart()
                    ->where($tables['identities'] . '.type', null)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('username', 'ASC')
            ->asArray()
            ->findAll();

        $groupsByUser = model(GroupModel::class)->getGroupsByUserIds(array_column($rows, 'id'));
        $groupTitles = $this->groupTitles();

        foreach ($rows as &$row) {
            $groups = $groupsByUser[$row['id']] ?? [];
            $row['groups'] = array_map(static fn ($g) => $groupTitles[$g] ?? $g, $groups);
        }
        unset($row);

        return $this->render('admin/users/index', ['items' => $rows]);
    }

    public function new()
    {
        return $this->render('admin/users/form', [
            'item' => null,
            'itemGroups' => [],
            'groups' => $this->groupTitles(),
        ]);
    }

    public function create()
    {
        $rules = (new ValidationRules())->getRegistrationRules();
        $rules['group'] = 'required|in_list[' . implode(',', array_keys($this->groupTitles())) . ']';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model(UserModel::class);
        $user = new ShieldUser([
            'username' => (string) $this->request->getPost('username'),
            'email'    => (string) $this->request->getPost('email'),
            'password' => (string) $this->request->getPost('password'),
        ]);

        $userModel->save($user);
        $user = $userModel->findById($userModel->getInsertID());
        $user->active = 1;
        $userModel->save($user);
        $user->addGroup((string) $this->request->getPost('group'));

        return redirect()->to('/cms/users')->with('message', 'Usuário criado.');
    }

    public function edit(int $id)
    {
        $user = model(UserModel::class)->findById($id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $tables = config('Auth')->tables;
        $identity = (new UserModel())
            ->select($tables['identities'] . '.secret')
            ->join($tables['identities'], $tables['users'] . '.id = ' . $tables['identities'] . '.user_id')
            ->where($tables['users'] . '.id', $id)
            ->where($tables['identities'] . '.type', Session::ID_TYPE_EMAIL_PASSWORD)
            ->asArray()
            ->first();
        $email = $identity['secret'] ?? null;

        $item = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $email,
            'active' => (int) $user->active,
        ];

        return $this->render('admin/users/form', [
            'item' => $item,
            'itemGroups' => $user->getGroups() ?? [],
            'groups' => $this->groupTitles(),
            'isSelf' => $id === (int) auth()->id(),
        ]);
    }

    public function update(int $id)
    {
        $userModel = model(UserModel::class);
        $user = $userModel->findById($id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $tables = config('Auth')->tables;
        $groupKeys = array_keys($this->groupTitles());
        $isSelf = $id === (int) auth()->id();

        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[{$tables['users']}.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[{$tables['identities']}.secret,user_id,{$id}]",
            'password' => 'permit_empty|min_length[8]',
            'password_confirm' => 'permit_empty|matches[password]',
        ];

        // The group field is only present (and required) when editing someone
        // else — self-edit locks the access level to avoid accidental lockout.
        if (! $isSelf) {
            $rules['group'] = 'required|in_list[' . implode(',', $groupKeys) . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user->username = (string) $this->request->getPost('username');
        $user->email = (string) $this->request->getPost('email');

        $newPassword = trim((string) $this->request->getPost('password'));
        if ($newPassword !== '') {
            $user->password = $newPassword;
        }

        // Users can't change their own access level or lock themselves out.
        if (! $isSelf) {
            $requestedGroup = (string) $this->request->getPost('group');
            $wasSuperAdmin = in_array('super_admin', $user->getGroups() ?? [], true);

            if ($wasSuperAdmin && $requestedGroup !== 'super_admin' && $this->superAdminCount() <= 1) {
                return redirect()->back()->withInput()->with('error', 'Não é possível remover o único super administrador restante.');
            }

            $user->active = $this->request->getPost('active') ? 1 : 0;
            $userModel->save($user);
            $user->syncGroups($requestedGroup);
        } else {
            $userModel->save($user);
        }

        return redirect()->to('/cms/users')->with('message', 'Usuário atualizado.');
    }

    public function delete(int $id)
    {
        if ($id === (int) auth()->id()) {
            return redirect()->to('/cms/users')->with('error', 'Você não pode excluir sua própria conta.');
        }

        $userModel = model(UserModel::class);
        $user = $userModel->findById($id);

        if ($user !== null) {
            $isSuperAdmin = in_array('super_admin', $user->getGroups() ?? [], true);

            if ($isSuperAdmin && $this->superAdminCount() <= 1) {
                return redirect()->to('/cms/users')->with('error', 'Não é possível excluir o único super administrador restante.');
            }

            $userModel->delete($id, true);
        }

        return redirect()->to('/cms/users')->with('message', 'Usuário excluído.');
    }

    /**
     * @return array<string, string> group key => display title
     */
    private function groupTitles(): array
    {
        $groups = setting('AuthGroups.groups') ?? [];

        return array_map(static fn ($g) => $g['title'] ?? $g, $groups);
    }

    private function superAdminCount(): int
    {
        return (int) model(GroupModel::class)->where('group', 'super_admin')->countAllResults();
    }
}
