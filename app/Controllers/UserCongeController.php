<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserCongeController extends BaseController
{
    /**
     * TODO: brancher sur l'auth réelle.
     */
    private function getEmployeId(): int
    {
        $id = session('employe_id');
        if (is_numeric($id)) {
            return (int) $id;
        }

        // Fallback (dev)
        return 3;
    }

    public function new(): string
    {
        $employeId = $this->getEmployeId();
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

    public function index(): string
    {
        $employeId = $this->getEmployeId();
        $statut    = (string) ($this->request->getGet('statut') ?? '');

        $allowedStatuts = ['en_attente', 'approuvee', 'refusee', 'annulee'];
        if ($statut !== '' && ! in_array($statut, $allowedStatuts, true)) {
            $statut = '';
        }

        $congeModel = new CongeModel();
        $builder    = $congeModel
            ->select('conges.*, types_conge.libelle as type_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->where('conges.employe_id', $employeId);

        if ($statut !== '') {
            $builder->where('conges.statut', $statut);
        }

        $demandes = $builder
            ->orderBy('conges.created_at', 'DESC')
            ->findAll();

        return view('user/mes-demandes', [
            'demandes' => $demandes,
            'statut'   => $statut,
        ]);
    }

    public function cancel(int $id): RedirectResponse
    {
        $employeId   = $this->getEmployeId();
        $congeModel  = new CongeModel();
        $demande     = $congeModel->where('id', $id)->where('employe_id', $employeId)->first();

        if (! $demande) {
            return redirect()->to('/user/conges')->with('error', 'Demande introuvable.');
        }

        if (($demande['statut'] ?? '') !== 'en_attente') {
            return redirect()->to('/user/conges')->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        $congeModel->update($id, [
            'statut'         => 'annulee',
            'commentaire_rh' => "Annulé par l'employé",
        ]);

        return redirect()->to('/user/conges')->with('success', 'Demande annulée.');
    }

    public function create(): RedirectResponse
    {
        $employeId = $this->getEmployeId();
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

        $tsDeb = strtotime($dateDebut);
        $tsFin = strtotime($dateFin);
        if ($tsDeb === false || $tsFin === false || $dateDebut > $dateFin) {
            return redirect()->back()->withInput()->with('error', 'Dates invalides : la date de début doit être antérieure à la date de fin.');
        }

        // Préavis minimum 48h
        if ($tsDeb < (time() + (48 * 3600))) {
            return redirect()->back()->withInput()->with('error', 'Préavis insuffisant : la demande doit être faite au moins 48h avant la date de début.');
        }

        $nbJours = (int) floor(($tsFin - $tsDeb) / 86400) + 1;
        if ($nbJours <= 0) {
            return redirect()->back()->withInput()->with('error', 'Nombre de jours invalide.');
        }

        // Vérification type
        $typeModel = new TypeCongeModel();
        $type      = $typeModel->find($typeCongeId);
        if (! $type) {
            return redirect()->back()->withInput()->with('error', 'Type de congé introuvable.');
        }

        // Chevauchement avec une demande existante (hors annulées/refusées)
        $congeModel = new CongeModel();
        $overlap = $congeModel
            ->where('employe_id', $employeId)
            ->whereNotIn('statut', ['refusee', 'annulee'])
            ->groupStart()
                ->where('date_debut <=', $dateFin)
                ->where('date_fin >=', $dateDebut)
            ->groupEnd()
            ->first();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'Chevauchement détecté avec une demande existante.');
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
}
