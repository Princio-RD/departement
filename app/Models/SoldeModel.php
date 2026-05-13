<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table            = 'soldes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employe_id', 
        'type_conge_id', 
        'annee', 
        'jours_attribues', 
        'jours_pris'
    ];

    protected $useTimestamps    = false;

    /**
     * Retourne le solde actuel pour un employé et une année
     * @return array{total_attribue: float, total_pris: float, restant: float}
     */
    public function getSoldeActuel(int $employeId, int $annee): array
    {
        $result = $this
            ->select('SUM(jours_attribues) as total_attribue, SUM(jours_pris) as total_pris')
            ->where('employe_id', $employeId)
            ->where('annee', $annee)
            ->first();

        $attribue = (float) ($result['total_attribue'] ?? 0);
        $pris = (float) ($result['total_pris'] ?? 0);

        return [
            'total_attribue' => $attribue,
            'total_pris' => $pris,
            'restant' => $attribue - $pris,
        ];
    }
}
