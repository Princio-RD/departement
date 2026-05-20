<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserCongeController extends BaseController
{
    public function new(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $employeId = (int) $user['id'];
        $annee     = (int) date('Y');

        $typeModel  = new TypeCongeModel();
        $soldeModel = new SoldeModel();

        $typesConge = $typeModel->findAll();

        $soldes = $soldeModel
            ->select('soldes.*, types_conge.libelle')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.employe_id', $employeId)
            ->where('soldes.annee', $annee)
            ->findAll();

        $soldesByType = [];
        foreach ($soldes as $s) {
            $attribues = (int) ($s['jours_attribues'] ?? 0);
            $pris      = (int) ($s['jours_pris'] ?? 0);
            $soldesByType[(int) $s['type_conge_id']] = [
                'attribues' => $attribues,
                'pris'      => $pris,
                'restant'   => max(0, $attribues - $pris),
            ];
        }

        return view('user/demande-conge', [
            'typesConge'   => $typesConge,
            'soldes'       => $soldes,
            'soldesByType' => $soldesByType,
        ]);
    }

    public function create(): RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $employeId = (int) $user['id'];
        $annee     = (int) date('Y');

        $rules = [
            'type_conge_id' => 'required|is_natural_no_zero',
            'date_debut'    => 'required|valid_date[Y-m-d]',
            'date_fin'      => 'required|valid_date[Y-m-d]',
            'motif'         => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut   = (string) $this->request->getPost('date_debut');
        $dateFin     = (string) $this->request->getPost('date_fin');
        $motif       = (string) $this->request->getPost('motif');

        if (strtotime($dateDebut) === false || strtotime($dateFin) === false || $dateDebut > $dateFin) {
            return redirect()->back()->withInput()->with('error', 'Dates invalides : la date de début doit être antérieure à la date de fin.');
        }

        $nbJours = (int) floor((strtotime($dateFin) - strtotime($dateDebut)) / 86400) + 1;
        if ($nbJours <= 0) {
            return redirect()->back()->withInput()->with('error', 'Nombre de jours invalide.');
        }

        // Vérification solde (si type déductible)
        $typeModel = new TypeCongeModel();
        $type      = $typeModel->find($typeCongeId);
        if (! $type) {
            return redirect()->back()->withInput()->with('error', 'Type de congé introuvable.');
        }

        $deductible = (int) ($type['deductible'] ?? 1) === 1;
        if ($deductible) {
            $soldeModel = new SoldeModel();
            $solde      = $soldeModel
                ->where('employe_id', $employeId)
                ->where('type_conge_id', $typeCongeId)
                ->where('annee', $annee)
                ->first();

            $attribues = (int) ($solde['jours_attribues'] ?? 0);
            $pris      = (int) ($solde['jours_pris'] ?? 0);
            $restant   = max(0, $attribues - $pris);

            if ($nbJours > $restant) {
                return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour ce type de congé.');
            }
        }

        // Création demande (statut en attente, solde non déduit ici)
        $congeModel = new CongeModel();
        $congeModel->insert([
            'employe_id'     => $employeId,
            'type_conge_id'  => $typeCongeId,
            'date_debut'     => $dateDebut,
            'date_fin'       => $dateFin,
            'nb_jours'       => $nbJours,
            'motif'          => $motif ?: null,
            'statut'         => 'en_attente',
            'commentaire_rh' => null,
            'traite_par'     => null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/user/conges/nouveau')->with('success', 'Demande envoyée et en attente de validation.');
    }

    /**
     * Liste des demandes de l'employé connecté.
     */
    public function index(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $employeId = (int) $user['id'];
        $statut = (string) ($this->request->getGet('statut') ?? '');

        $congeModel = new CongeModel();
        $builder = $congeModel
            ->select('conges.*, types_conge.libelle as type_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.employe_id', $employeId);

        if ($statut !== '') {
            $builder->where('conges.statut', $statut);
        }

        $demandes = $builder->orderBy('conges.created_at', 'DESC')->findAll();

        $stats = [
            'total' => count($demandes),
            'en_attente' => count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'en_attente')),
            'approuvee' => count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'approuvee')),
            'refusee' => count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'refusee')),
            'annulee' => count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'annulee')),
            'jours_total' => array_sum(array_map(static fn($d) => (int) ($d['nb_jours'] ?? 0), $demandes)),
        ];

        $typeStats = [];
        foreach ($demandes as $demande) {
            $label = (string) ($demande['type_libelle'] ?? 'Non défini');
            $typeStats[$label] = ($typeStats[$label] ?? 0) + 1;
        }
        arsort($typeStats);

        return view('user/mes-demandes', [
            'demandes' => $demandes,
            'filters' => [
                'statut' => $statut,
            ],
            'stats' => $stats,
            'typeStats' => $typeStats,
        ]);
    }

    /**
     * Annuler une demande en attente.
     */
    public function cancel(int $id): RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $employeId = (int) $user['id'];

        $congeModel = new CongeModel();
        $demande = $congeModel->where('id', $id)->where('employe_id', $employeId)->first();
        if (! $demande) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }

        if (($demande['statut'] ?? '') !== 'en_attente') {
            return redirect()->back()->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        // On conserve l'historique: on passe le statut à 'annulee'
        $congeModel->update($id, ['statut' => 'annulee']);

        return redirect()->to('/user/conges')->with('success', 'Demande annulée.');
    }
}
