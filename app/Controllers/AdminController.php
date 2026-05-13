<?php

namespace App\Controllers;

use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdminController extends BaseController
{
    public function dashboard(): string
    {
        // Vue statique fournie (HTML). Pour l'instant on la sert telle quelle.
        return view('admin/dashboard-admin');
    }

    public function employes(): string
    {
        // Vue statique fournie (HTML). Elle sera rendue dynamique ensuite.
        return view('admin/gestion-employes');
    }

    /**
     * Création d'un employé + initialisation des soldes annuels.
     */
    public function createEmploye(): RedirectResponse
    {
        $rules = [
            'prenom'         => 'required|min_length[2]|max_length[60]',
            'nom'            => 'required|min_length[2]|max_length[60]',
            'email'          => 'required|valid_email|max_length[120]',
            'password'       => 'required|min_length[3]|max_length[255]',
            'departement_id' => 'required|is_natural_no_zero',
            'role'           => 'required|in_list[employe,rh,admin]',
            'date_embauche'  => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $prenom = (string) $this->request->getPost('prenom');
        $nom = (string) $this->request->getPost('nom');
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');
        $departementId = (int) $this->request->getPost('departement_id');
        $role = (string) $this->request->getPost('role');
        $dateEmbauche = (string) ($this->request->getPost('date_embauche') ?? '');

        $employeModel = new EmployeModel();

        // Unicité email (simple)
        $exists = $employeModel->where('LOWER(email)', mb_strtolower(trim($email)))->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Cet email est déjà utilisé.');
        }

        $employeId = (int) $employeModel->insert([
            'prenom'         => $prenom,
            'nom'            => $nom,
            'email'          => $email,
            'password'       => password_hash($password, PASSWORD_DEFAULT),
            'departement_id' => $departementId,
            'role'           => $role,
            'date_embauche'  => $dateEmbauche !== '' ? $dateEmbauche : null,
            'actif'          => 1,
        ], true);

        if ($employeId <= 0) {
            return redirect()->back()->withInput()->with('error', "Échec de création de l'employé.");
        }

        // Initialisation soldes pour l'année courante
        $annee = (int) date('Y');
        $typeModel = new TypeCongeModel();
        $types = $typeModel->findAll();

        $soldeModel = new SoldeModel();
        foreach ($types as $t) {
            $typeId = (int) ($t['id'] ?? 0);
            if ($typeId <= 0) {
                continue;
            }

            $soldeModel->insert([
                'employe_id'     => $employeId,
                'type_conge_id'  => $typeId,
                'annee'          => $annee,
                'jours_attribues'=> (int) ($t['jours_annuels'] ?? 0),
                'jours_pris'     => 0,
            ]);
        }

        return redirect()->to('/admin/employes')->with('success', 'Employé créé et soldes initialisés.');
    }

    /**
     * Active/Désactive un employé.
     */
    public function toggleEmploye(int $id): RedirectResponse
    {
        $employeModel = new EmployeModel();
        $emp = $employeModel->find($id);
        if (! $emp) {
            return redirect()->back()->with('error', 'Employé introuvable.');
        }

        $newActif = ((int) ($emp['actif'] ?? 1) === 1) ? 0 : 1;
        $employeModel->update($id, ['actif' => $newActif]);

        return redirect()->back()->with('success', $newActif ? 'Employé réactivé.' : 'Employé désactivé.');
    }
}
