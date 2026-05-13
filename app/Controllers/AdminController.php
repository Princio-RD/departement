<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdminController extends BaseController
{
    public function dashboard(): string
    {
        $annee = (int) date('Y');
        $today = date('Y-m-d');

        $employeModel = new EmployeModel();
        $congeModel = new CongeModel();
        $deptModel = new DepartementModel();

        $stats = [
            'employes_actifs' => $employeModel->where('actif', 1)->countAllResults(),
            'demandes_en_attente' => (new CongeModel())->where('statut', 'en_attente')->countAllResults(),
            // approx: demandes approuvées ce mois
            'approuvees_mois' => (new CongeModel())
                ->where('statut', 'approuvee')
                ->like('created_at', date('Y-m'), 'after')
                ->countAllResults(),
            'departements' => $deptModel->countAllResults(),
        ];

        // Absents aujourd'hui = congés approuvés dont today est dans [date_debut, date_fin]
        $absents = $congeModel
            ->select('conges.*, employees.nom, employees.prenom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = conges.employe_id')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.statut', 'approuvee')
            ->where('conges.date_debut <=', $today)
            ->where('conges.date_fin >=', $today)
            ->orderBy('conges.date_fin', 'ASC')
            ->findAll();
        $stats['absents_aujourdhui'] = is_array($absents) ? count($absents) : 0;

        $lastDemandes = (new CongeModel())
            ->select('conges.*, employees.nom, employees.prenom, departements.nom as departement_nom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = conges.employe_id')
            ->join('departements', 'departements.id = employees.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->orderBy('conges.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return view('admin/dashboard-admin', [
            'stats' => $stats,
            'absents' => $absents,
            'lastDemandes' => $lastDemandes,
            'annee' => $annee,
        ]);
    }

    public function employes(): string
    {
        $deptModel = new DepartementModel();
        $departements = $deptModel->orderBy('nom', 'ASC')->findAll();

        $deptId = (int) ($this->request->getGet('departement_id') ?? 0);
        $q = trim((string) ($this->request->getGet('q') ?? ''));

        $employeModel = new EmployeModel();
        $builder = $employeModel
            ->select('employees.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employees.departement_id', 'left');

        if ($deptId > 0) {
            $builder->where('employees.departement_id', $deptId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('employees.nom', $q)
                ->orLike('employees.prenom', $q)
                ->orLike('employees.email', $q)
                ->groupEnd();
        }

        $employes = $builder->orderBy('employees.actif', 'DESC')->orderBy('employees.nom', 'ASC')->findAll();

        return view('admin/gestion-employes', [
            'employes' => $employes,
            'departements' => $departements,
            'filters' => [
                'departement_id' => $deptId,
                'q' => $q,
            ],
        ]);
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

    /**
     * Liste des départements (à implémenter)
     */
    public function departements(): string
    {
        // TODO: implémenter la gestion des départements
        return view('admin/departements', [
            'departements' => (new DepartementModel())->orderBy('nom', 'ASC')->findAll()
        ]);
    }

    /**
     * Liste des types de congé (à implémenter)
     */
    public function typesConge(): string
    {
        // TODO: implémenter la gestion des types de congé
        return view('admin/types-conge', [
            'types' => (new TypeCongeModel())->findAll()
        ]);
    }

    /**
     * Vue des soldes annuels (à implémenter)
     */
    public function soldes(): string
    {
        // TODO: implémenter la vue des soldes
        $annee = (int) date('Y');
        return view('admin/soldes', [
            'annee' => $annee,
            'soldes' => [] // à implémenter
        ]);
    }

    /**
     * Validation des demandes (vue unifiée RH/Admin)
     */
    public function validationRh(): string
    {
        // Redirige vers la vue de validation RH (partagée)
        return view('RH/validation-rh', [
            'isAdmin' => true
        ]);
    }
}
