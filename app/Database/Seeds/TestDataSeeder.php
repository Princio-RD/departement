<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Départements
        $deptModel = \App\Models\DepartementModel::class;
        $departements = [
            ['nom' => 'IT', 'description' => 'Informatique et Technologies'],
            ['nom' => 'Finance', 'description' => 'Gestion financière et comptabilité'],
            ['nom' => 'Marketing', 'description' => 'Marketing et communication'],
            ['nom' => 'RH', 'description' => 'Ressources humaines'],
        ];
        $this->db->table('departements')->insertBatch($departements);

        // Types de congé
        $typeModel = \App\Models\TypeCongeModel::class;
        $types = [
            ['libelle' => 'Annuel', 'jours_annuels' => 30, 'deductible' => 0],
            ['libelle' => 'Maladie', 'jours_annuels' => 30, 'deductible' => 0],
            ['libelle' => 'Maternité', 'jours_annuels' => 90, 'deductible' => 0],
            ['libelle' => 'Paternité', 'jours_annuels' => 5, 'deductible' => 0],
            ['libelle' => 'Spécial', 'jours_annuels' => 10, 'deductible' => 0],
        ];
        $this->db->table('types_conge')->insertBatch($types);

        // Employés
        $empModel = \App\Models\EmployeModel::class;
        $employees = [
            [
                'prenom' => 'Soa',
                'nom' => 'Rakoto',
                'email' => 'soa@techmada.mg',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'departement_id' => 1,
                'role' => 'employe',
                'date_embauche' => '2022-03-01',
                'actif' => 1,
            ],
            [
                'prenom' => 'Marie',
                'nom' => 'Rabe',
                'email' => 'rh@techmada.mg',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'departement_id' => 4,
                'role' => 'rh',
                'date_embauche' => '2020-01-15',
                'actif' => 1,
            ],
            [
                'prenom' => 'Tsiry',
                'nom' => 'Fidy',
                'email' => 'tsiry@techmada.mg',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'departement_id' => 2,
                'role' => 'employe',
                'date_embauche' => '2019-07-10',
                'actif' => 0,
            ],
            [
                'prenom' => 'Haja',
                'nom' => 'Andria',
                'email' => 'haja@techmada.mg',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'departement_id' => 3,
                'role' => 'employe',
                'date_embauche' => '2023-01-20',
                'actif' => 1,
            ],
        ];
        $this->db->table('employees')->insertBatch($employees);

        // Soldes pour l'année courante
        $soldeModel = \App\Models\SoldeModel::class;
        $annee = (int) date('Y');
        $typesList = $this->db->table('types_conge')->get()->getResultArray();
        $employeesList = $this->db->table('employees')->get()->getResultArray();

        foreach ($employeesList as $emp) {
            foreach ($typesList as $type) {
                $this->db->table('soldes')->insert([
                    'employe_id' => $emp['id'],
                    'type_conge_id' => $type['id'],
                    'annee' => $annee,
                    'jours_attribues' => $type['jours_annuels'],
                    'jours_pris' => 0,
                ]);
            }
        }

        // Quelques congés pour les tests
        $congeModel = \App\Models\CongeModel::class;
        $today = date('Y-m-d');
        $demandes = [
            [
                'employe_id' => 1,
                'type_conge_id' => 1,
                'date_debut' => date('Y-m-d', strtotime('-2 days')),
                'date_fin' => date('Y-m-d', strtotime('+3 days')),
                'motif' => 'Vacances',
                'statut' => 'approuvee',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'employe_id' => 3,
                'type_conge_id' => 2,
                'date_debut' => date('Y-m-d', strtotime('-1 day')),
                'date_fin' => date('Y-m-d', strtotime('today')),
                'motif' => 'Rendez-vous médical',
                'statut' => 'en_attente',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
        ];
        foreach ($demandes as $d) {
            $this->db->table('conges')->insert($d);
        }
    }
}
