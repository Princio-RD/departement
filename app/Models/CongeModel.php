<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table            = 'conges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employe_id', 
        'type_conge_id', 
        'date_debut', 
        'date_fin', 
        'nb_jours', 
        'motif', 
        'statut', 
        'commentaire_rh', 
        'traite_par'
    ];

  
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; 
}
