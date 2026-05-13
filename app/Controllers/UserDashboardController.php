<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;

class UserDashboardController extends BaseController
{
    public function index(): string
    {
        $user = session()->get('user');
        $employeId = (int) ($user['id'] ?? 0);
        $annee = (int) date('Y');

        $stats = [
            'en_attente' => (new CongeModel())->where('employe_id', $employeId)->where('statut', 'en_attente')->countAllResults(),
            'approuvee'  => (new CongeModel())->where('employe_id', $employeId)->where('statut', 'approuvee')->countAllResults(),
            'refusee'    => (new CongeModel())->where('employe_id', $employeId)->where('statut', 'refusee')->countAllResults(),
            'annulee'    => (new CongeModel())->where('employe_id', $employeId)->where('statut', 'annulee')->countAllResults(),
        ];

        $soldes = (new SoldeModel())
            ->select('soldes.*, types_conge.libelle')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.employe_id', $employeId)
            ->where('soldes.annee', $annee)
            ->orderBy('types_conge.libelle', 'ASC')
            ->findAll();

        $lastDemandes = (new CongeModel())
            ->select('conges.*, types_conge.libelle as type_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.employe_id', $employeId)
            ->orderBy('conges.created_at', 'DESC')
            ->limit(8)
            ->findAll();

        return view('user/dashboard', [
            'stats' => $stats,
            'soldes' => $soldes,
            'lastDemandes' => $lastDemandes,
            'annee' => $annee,
        ]);
    }
}
