<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserDashboardController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

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

    /**
     * Calendrier interactif des congés de l'employé connecté.
     */
    public function calendar(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (! $user || empty($user['id'])) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $employeId = (int) $user['id'];
        $annee = (int) date('Y');

        $conges = (new CongeModel())
            ->select('conges.*, types_conge.libelle as type_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.employe_id', $employeId)
            ->orderBy('conges.date_debut', 'ASC')
            ->findAll();

        $events = [];
        foreach ($conges as $conge) {
            $statut = (string) ($conge['statut'] ?? '');
            $color = match ($statut) {
                'approuvee' => '#1f8a5b',
                'refusee' => '#c0392b',
                'annulee' => '#6c757d',
                default => '#c27d1f',
            };

            $start = (string) ($conge['date_debut'] ?? '');
            $end = (string) ($conge['date_fin'] ?? '');
            if ($start === '' || $end === '') {
                continue;
            }

            $endExclusive = (new \DateTimeImmutable($end))->modify('+1 day')->format('Y-m-d');

            $events[] = [
                'title' => trim((string) ($conge['type_libelle'] ?? 'Congé') . ' · ' . $statut),
                'start' => $start,
                'end' => $endExclusive,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'status' => $statut,
                    'motif' => (string) ($conge['motif'] ?? ''),
                    'jours' => (int) ($conge['nb_jours'] ?? 0),
                    'type' => (string) ($conge['type_libelle'] ?? ''),
                ],
            ];
        }

        $summary = [
            'total' => count($conges),
            'en_attente' => count(array_filter($conges, static fn($c) => ($c['statut'] ?? '') === 'en_attente')),
            'approuvee' => count(array_filter($conges, static fn($c) => ($c['statut'] ?? '') === 'approuvee')),
            'refusee' => count(array_filter($conges, static fn($c) => ($c['statut'] ?? '') === 'refusee')),
            'annulee' => count(array_filter($conges, static fn($c) => ($c['statut'] ?? '') === 'annulee')),
        ];

        $typeStats = [];
        foreach ($conges as $conge) {
            $label = (string) ($conge['type_libelle'] ?? 'Non défini');
            $typeStats[$label] = ($typeStats[$label] ?? 0) + 1;
        }
        arsort($typeStats);

        return view('user/calendrier', [
            'annee' => $annee,
            'eventsJson' => json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'summary' => $summary,
            'typeStats' => $typeStats,
        ]);
    }
}
