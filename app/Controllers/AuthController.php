<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function loginForm(): string|RedirectResponse
    {
        // Si déjà connecté, rediriger selon rôle
        if (session()->get('user')) {
            return redirect()->to($this->redirectByRole((string) session()->get('user.role')));
        }

        return view('login');
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[3]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $model = new EmployeModel();
        $user  = $model->findActiveByEmail($email);

        if (! $user || ! $model->verifyPassword($password, $user['password'] ?? null)) {
            return redirect()->back()->withInput()->with('error', 'Identifiants incorrects.');
        }

        // Stockage minimal en session
        session()->set([
            'user' => [
                'id'             => (int) ($user['id'] ?? 0),
                'nom'            => (string) ($user['nom'] ?? ''),
                'prenom'         => (string) ($user['prenom'] ?? ''),
                'role'           => (string) ($user['role'] ?? 'employe'),
                'departement_id' => (int) ($user['departement_id'] ?? 0),
            ],
        ]);

        return redirect()->to($this->redirectByRole((string) ($user['role'] ?? 'employe')))
            ->with('success', 'Connexion réussie.');
    }

    public function logout(): RedirectResponse
    {
        session()->remove('user');
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Déconnexion effectuée.');
    }

    private function redirectByRole(string $role): string
    {
        $role = strtolower(trim($role));

        return match ($role) {
            'admin' => '/admin',
            'rh'    => '/rh',
            default => '/user',
        };
    }
}
