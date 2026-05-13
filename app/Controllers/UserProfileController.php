<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserProfileController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $model = new EmployeModel();
        $emp = $model->find((int) $user['id']);

        return view('user/profil', [
            'employe' => $emp,
        ]);
    }

    public function updatePassword(): RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'password' => 'required|min_length[3]|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $password = (string) $this->request->getPost('password');

        (new EmployeModel())->update((int) $user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirect()->back()->with('success', 'Mot de passe mis à jour.');
    }
}
