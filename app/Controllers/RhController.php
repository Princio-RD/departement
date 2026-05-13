<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class RhController extends BaseController
{
    /**
     * Liste des demandes à traiter (en attente) + filtres.
     */
    public function index(): string
    {
        $deptId = (int) ($this->request->getGet('departement_id') ?? 0);
        $statut = (string) ($this->request->getGet('statut') ?? 'en_attente');

        $congeModel = new CongeModel();

        $builder = $congeModel
            ->select('conges.*, employees.nom, employees.prenom, employees.departement_id, departements.nom as departement_nom, types_conge.libelle as type_libelle, types_conge.deductible')
            ->join('employees', 'employees.id = conges.employe_id')
            ->join('departements', 'departements.id = employees.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id');

        if ($statut !== '') {
            $builder->where('conges.statut', $statut);
        }
        if ($deptId > 0) {
            $builder->where('employees.departement_id', $deptId);
        }

        $demandes = $builder->orderBy('conges.created_at', 'DESC')->findAll();

        // soldes restant par demande (si déductible)
        $annee = (int) date('Y');
        $soldeModel = new SoldeModel();
        $soldesRestants = [];

        foreach ($demandes as $d) {
            if ((int) ($d['deductible'] ?? 1) !== 1) {
                $soldesRestants[(int) $d['id']] = null;
                continue;
            }

            $solde = $soldeModel
                ->where('employe_id', (int) $d['employe_id'])
                ->where('type_conge_id', (int) $d['type_conge_id'])
                ->where('annee', $annee)
                ->first();

            $attribues = (int)($solde['jours_attribues'] ?? 0);
            $pris = (int)($solde['jours_pris'] ?? 0);
            $soldesRestants[(int) $d['id']] = max(0, $attribues - $pris);
        }

        $deptModel = new \App\Models\DepartementModel();
        $departements = $deptModel->findAll();

        $counts = [
            'en_attente' => $congeModel->where('statut', 'en_attente')->countAllResults(false),
        ];
        // reset builder due to countAllResults(false)
        $congeModel->builder()->resetQuery();
        $counts['all'] = (new CongeModel())->countAllResults();
        $counts['approuvee'] = (new CongeModel())->where('statut', 'approuvee')->countAllResults();
        $counts['refusee'] = (new CongeModel())->where('statut', 'refusee')->countAllResults();

        return view('RH/validation-rh', [
            'demandes' => $demandes,
            'departements' => $departements,
            'filters' => [
                'departement_id' => $deptId,
                'statut' => $statut,
            ],
            'soldesRestants' => $soldesRestants,
            'counts' => $counts,
        ]);
    }

    /**
     * Dashboard RH (vue d'ensemble).
     */
    public function dashboard(): string
    {
        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $user = session()->get('user');
        $rhId = (int) ($user['id'] ?? 0);

        $stats = [
            'en_attente' => (new CongeModel())->where('statut', 'en_attente')->countAllResults(),
            'approuvee'  => (new CongeModel())->where('statut', 'approuvee')->countAllResults(),
            'refusee'    => (new CongeModel())->where('statut', 'refusee')->countAllResults(),
        ];

        // Dernières demandes
        $lastDemandes = $congeModel
            ->select('conges.*, employees.nom, employees.prenom, departements.nom as departement_nom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = conges.employe_id')
            ->join('departements', 'departements.id = employees.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->orderBy('conges.created_at', 'DESC')
            ->limit(8)
            ->findAll();

        // Soldes: top 8 soldes restants (approx) pour l'année courante
        $annee = (int) date('Y');
        $soldes = $soldeModel
            ->select('soldes.*, employees.nom, employees.prenom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = soldes.employe_id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.annee', $annee)
            ->orderBy('(soldes.jours_attribues - soldes.jours_pris)', 'DESC', false)
            ->limit(12)
            ->findAll();

        return view('RH/dashboard-employe', [
            'stats' => $stats,
            'lastDemandes' => $lastDemandes,
            'soldes' => $soldes,
            'annee' => $annee,
            'rhId' => $rhId,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $user = session()->get('user');
        $rhId = (int) ($user['id'] ?? 0);

        $congeModel = new CongeModel();
        $demande = $congeModel
            ->select('conges.*, types_conge.deductible')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.id', $id)
            ->first();

        if (! $demande) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }
        if (($demande['statut'] ?? '') !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $comment = (string) $this->request->getPost('commentaire_rh');

        // MAJ statut
        $congeModel->update($id, [
            'statut' => 'approuvee',
            'commentaire_rh' => $comment !== '' ? $comment : null,
            'traite_par' => $rhId ?: null,
        ]);

        // Déduction solde si déductible
        if ((int) ($demande['deductible'] ?? 1) === 1) {
            $annee = (int) date('Y');
            $soldeModel = new SoldeModel();
            $solde = $soldeModel
                ->where('employe_id', (int) $demande['employe_id'])
                ->where('type_conge_id', (int) $demande['type_conge_id'])
                ->where('annee', $annee)
                ->first();

            if ($solde) {
                $joursPris = (int) ($solde['jours_pris'] ?? 0);
                $soldeModel->update((int) $solde['id'], [
                    'jours_pris' => $joursPris + (int) ($demande['nb_jours'] ?? 0),
                ]);
            }
        }

        return redirect()->to('/rh')->with('success', 'Demande approuvée.');
    }

    public function refuse(int $id): RedirectResponse
    {
        $user = session()->get('user');
        $rhId = (int) ($user['id'] ?? 0);

        $rules = [
            'commentaire_rh' => 'permit_empty|max_length[500]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $congeModel = new CongeModel();
        $demande = $congeModel->find($id);
        if (! $demande) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }
        if (($demande['statut'] ?? '') !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $comment = (string) $this->request->getPost('commentaire_rh');

        $congeModel->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $comment !== '' ? $comment : null,
            'traite_par' => $rhId ?: null,
        ]);

        return redirect()->to('/rh')->with('success', 'Demande refusée.');
    }

    /**
     * Historique des demandes (toutes sauf en_attente par défaut).
     */
    public function historique(): string
    {
        $deptId = (int) ($this->request->getGet('departement_id') ?? 0);
        $statut = (string) ($this->request->getGet('statut') ?? '');

        $congeModel = new CongeModel();

        $builder = $congeModel
            ->select('conges.*, employees.nom, employees.prenom, employees.departement_id, departements.nom as departement_nom, types_conge.libelle as type_libelle')
            ->join('employees', 'employees.id = conges.employe_id')
            ->join('departements', 'departements.id = employees.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id');

        // par défaut: historique = tout sauf en attente
        if ($statut !== '') {
            $builder->where('conges.statut', $statut);
        } else {
            $builder->where('conges.statut !=', 'en_attente');
        }

        if ($deptId > 0) {
            $builder->where('employees.departement_id', $deptId);
        }

        $demandes = $builder->orderBy('conges.created_at', 'DESC')->findAll();

        $deptModel = new \App\Models\DepartementModel();
        $departements = $deptModel->findAll();

        return view('RH/historique', [
            'demandes' => $demandes,
            'departements' => $departements,
            'filters' => [
                'departement_id' => $deptId,
                'statut' => $statut,
            ],
        ]);
    }

    /**
     * Tableau des soldes des employés (année courante).
     */
    public function soldes(): string
    {
        $annee = (int) ($this->request->getGet('annee') ?? date('Y'));
        $deptId = (int) ($this->request->getGet('departement_id') ?? 0);

        $soldeModel = new SoldeModel();
        $builder = $soldeModel
            ->select('soldes.*, employees.nom, employees.prenom, employees.departement_id, departements.nom as departement_nom, types_conge.libelle as type_libelle, types_conge.deductible')
            ->join('employees', 'employees.id = soldes.employe_id')
            ->join('departements', 'departements.id = employees.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.annee', $annee);

        if ($deptId > 0) {
            $builder->where('employees.departement_id', $deptId);
        }

        $soldes = $builder
            ->orderBy('departements.nom', 'ASC')
            ->orderBy('employees.nom', 'ASC')
            ->orderBy('types_conge.libelle', 'ASC')
            ->findAll();

        $deptModel = new \App\Models\DepartementModel();
        $departements = $deptModel->findAll();

        return view('RH/soldes', [
            'soldes' => $soldes,
            'departements' => $departements,
            'filters' => [
                'annee' => $annee,
                'departement_id' => $deptId,
            ],
        ]);
    }
}
