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
            'demandes_traitees' => (new CongeModel())
                ->groupStart()
                ->where('statut', 'approuvee')
                ->orWhere('statut', 'refusee')
                ->groupEnd()
                ->countAllResults(),
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

        $conges = (new CongeModel())
            ->select('date_debut, date_fin, statut')
            ->where('statut', 'approuvee')
            ->findAll();

        $monthly = array_fill(1, 12, 0);
        $weekly = array_fill(1, 7, 0);

        foreach ($conges as $conge) {
            $start = (string) ($conge['date_debut'] ?? '');
            $end = (string) ($conge['date_fin'] ?? '');
            if ($start === '' || $end === '') {
                continue;
            }

            try {
                $cursor = new \DateTimeImmutable($start);
                $limit = new \DateTimeImmutable($end);
            } catch (\Throwable) {
                continue;
            }

            while ($cursor <= $limit) {
                if ((int) $cursor->format('Y') === $annee) {
                    $month = (int) $cursor->format('n');
                    $weekday = (int) $cursor->format('N');
                    $monthly[$month]++;
                    $weekly[$weekday]++;
                }
                $cursor = $cursor->modify('+1 day');
            }
        }

        return view('admin/dashboard-admin', [
            'stats' => $stats,
            'absents' => $absents,
            'lastDemandes' => $lastDemandes,
            'annee' => $annee,
            'monthlyLeaveData' => array_values($monthly),
            'weekdayLeaveData' => array_values($weekly),
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
                'jours_attribues' => (int) ($t['jours_annuels'] ?? 0),
                'jours_pris'     => 0,
            ]);
        }

        return redirect()->to('/admin/employes')->with('success', 'Employé créé et soldes initialisés.');
    }

    /**
     * Formulaire d'édition d'un employé.
     */
    public function editEmploye(int $id): string|RedirectResponse
    {
        $employeModel = new EmployeModel();
        $emp = $employeModel->find($id);

        if (! $emp) {
            return redirect()->to('/admin/employes')->with('error', 'Employé introuvable.');
        }

        $departements = (new DepartementModel())->orderBy('nom', 'ASC')->findAll();

        return view('admin/edit-employe', [
            'emp' => $emp,
            'departements' => $departements,
        ]);
    }

    /**
     * Mise à jour d'un employé.
     */
    public function updateEmploye(int $id): RedirectResponse
    {
        $employeModel = new EmployeModel();
        $emp = $employeModel->find($id);

        if (! $emp) {
            return redirect()->to('/admin/employes')->with('error', 'Employé introuvable.');
        }

        $rules = [
            'prenom'         => 'required|min_length[2]|max_length[60]',
            'nom'            => 'required|min_length[2]|max_length[60]',
            'email'          => 'required|valid_email|max_length[120]',
            'departement_id' => 'required|is_natural_no_zero',
            'role'           => 'required|in_list[employe,rh,admin]',
            'date_embauche'  => 'permit_empty|valid_date[Y-m-d]',
            'password'       => 'permit_empty|min_length[3]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $prenom = (string) $this->request->getPost('prenom');
        $nom = (string) $this->request->getPost('nom');
        $email = (string) $this->request->getPost('email');
        $password = trim((string) $this->request->getPost('password'));
        $departementId = (int) $this->request->getPost('departement_id');
        $role = (string) $this->request->getPost('role');
        $dateEmbauche = (string) ($this->request->getPost('date_embauche') ?? '');
        $actif = (int) ($this->request->getPost('actif') ?? ($emp['actif'] ?? 1));

        $currentEmail = mb_strtolower(trim((string) ($emp['email'] ?? '')));
        $newEmail = mb_strtolower(trim($email));

        if ($newEmail !== $currentEmail) {
            $exists = $employeModel->where('LOWER(email)', $newEmail)->where('id !=', $id)->first();
            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Cet email est déjà utilisé.');
            }
        }

        $data = [
            'prenom'         => $prenom,
            'nom'            => $nom,
            'email'          => $email,
            'departement_id' => $departementId,
            'role'           => $role,
            'date_embauche'  => $dateEmbauche !== '' ? $dateEmbauche : null,
            'actif'          => $actif ? 1 : 0,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $employeModel->update($id, $data);

        return redirect()->to('/admin/employes')->with('success', 'Employé modifié avec succès.');
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
        return view('admin/departements', [
            'departements' => (new DepartementModel())
                ->select('departements.*, COUNT(employees.id) as employe_count')
                ->join('employees', 'employees.departement_id = departements.id', 'left')
                ->groupBy('departements.id')
                ->orderBy('nom', 'ASC')
                ->findAll()
        ]);
    }

    /**
     * Formulaire d'édition d'un département.
     */
    public function editDepartement(int $id): string|RedirectResponse
    {
        $dept = (new DepartementModel())->find($id);
        if (! $dept) {
            return redirect()->to('/admin/departements')->with('error', 'Département introuvable.');
        }

        return view('admin/edit-departement', ['dept' => $dept]);
    }

    /**
     * Mise à jour d'un département.
     */
    public function updateDepartement(int $id): RedirectResponse
    {
        $model = new DepartementModel();
        $dept = $model->find($id);
        if (! $dept) {
            return redirect()->to('/admin/departements')->with('error', 'Département introuvable.');
        }

        $rules = [
            'nom' => 'required|min_length[2]|max_length[120]',
            'description' => 'permit_empty|max_length[500]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $description = trim((string) $this->request->getPost('description'));

        $exists = $model->where('LOWER(nom)', mb_strtolower($nom))->where('id !=', $id)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Ce département existe déjà.');
        }

        $model->update($id, [
            'nom' => $nom,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to('/admin/departements')->with('success', 'Département modifié avec succès.');
    }

    /**
     * Liste des types de congé (à implémenter)
     */
    public function typesConge(): string
    {
        return view('admin/types-conge', [
            'types' => (new TypeCongeModel())->findAll()
        ]);
    }

    /**
     * Formulaire d'édition d'un type de congé.
     */
    public function editTypeConge(int $id): string|RedirectResponse
    {
        $type = (new TypeCongeModel())->find($id);
        if (! $type) {
            return redirect()->to('/admin/types-conge')->with('error', 'Type de congé introuvable.');
        }

        return view('admin/edit-type-conge', ['type' => $type]);
    }

    /**
     * Mise à jour d'un type de congé.
     */
    public function updateTypeConge(int $id): RedirectResponse
    {
        $model = new TypeCongeModel();
        $type = $model->find($id);
        if (! $type) {
            return redirect()->to('/admin/types-conge')->with('error', 'Type de congé introuvable.');
        }

        $rules = [
            'libelle' => 'required|min_length[2]|max_length[120]',
            'jours_annuels' => 'required|numeric',
            'deductible' => 'required|in_list[0,1]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $libelle = trim((string) $this->request->getPost('libelle'));
        $jours = (float) $this->request->getPost('jours_annuels');
        $deductible = (int) $this->request->getPost('deductible');

        $exists = $model->where('LOWER(libelle)', mb_strtolower($libelle))->where('id !=', $id)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Ce type de congé existe déjà.');
        }

        $model->update($id, [
            'libelle' => $libelle,
            'jours_annuels' => $jours,
            'deductible' => $deductible,
        ]);

        return redirect()->to('/admin/types-conge')->with('success', 'Type de congé modifié avec succès.');
    }

    /**
     * Vue des soldes annuels (à implémenter)
     */
    public function soldes(): string
    {
        $annee = (int) date('Y');
        return view('admin/soldes', [
            'annee' => $annee,
            'soldes' => (new SoldeModel())
                ->select('soldes.*, employees.nom as employe_nom, employees.prenom as employe_prenom, types_conge.libelle as type_libelle')
                ->join('employees', 'employees.id = soldes.employe_id')
                ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
                ->where('soldes.annee', $annee)
                ->orderBy('employees.nom', 'ASC')
                ->orderBy('types_conge.libelle', 'ASC')
                ->findAll()
        ]);
    }

    /**
     * Formulaire d'édition d'un solde.
     */
    public function editSolde(int $id): string|RedirectResponse
    {
        $solde = (new SoldeModel())
            ->select('soldes.*, employees.nom as employe_nom, employees.prenom as employe_prenom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = soldes.employe_id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.id', $id)
            ->first();

        if (! $solde) {
            return redirect()->to('/admin/soldes')->with('error', 'Solde introuvable.');
        }

        return view('admin/edit-solde', ['solde' => $solde]);
    }

    /**
     * Mise à jour d'un solde.
     */
    public function updateSolde(int $id): RedirectResponse
    {
        $model = new SoldeModel();
        $solde = $model->find($id);
        if (! $solde) {
            return redirect()->to('/admin/soldes')->with('error', 'Solde introuvable.');
        }

        $rules = [
            'annee' => 'required|is_natural_no_zero',
            'jours_attribues' => 'required|numeric',
            'jours_pris' => 'required|numeric',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $annee = (int) $this->request->getPost('annee');
        $attribues = (float) $this->request->getPost('jours_attribues');
        $pris = (float) $this->request->getPost('jours_pris');

        if ($pris < 0 || $attribues < 0 || $pris > $attribues) {
            return redirect()->back()->withInput()->with('error', 'Le nombre de jours pris doit être compris entre 0 et les jours attribués.');
        }

        $model->update($id, [
            'annee' => $annee,
            'jours_attribues' => $attribues,
            'jours_pris' => $pris,
        ]);

        return redirect()->to('/admin/soldes')->with('success', 'Solde modifié avec succès.');
    }

    /**
     * Validation des demandes (vue unifiée RH/Admin)
     */
    public function validationRh(): string|RedirectResponse
    {
        return redirect()->to('/rh');
    }
}
