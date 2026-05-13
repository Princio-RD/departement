<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');
        if (! $user) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        if (is_array($arguments) && ! empty($arguments)) {
            $role = strtolower((string) ($user['role'] ?? ''));
            $allowed = array_map(static fn ($r) => strtolower(trim((string) $r)), $arguments);

            if (! in_array($role, $allowed, true)) {
                return redirect()->to('/login')->with('error', 'Accès non autorisé.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
