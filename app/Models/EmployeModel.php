<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
  
    protected $allowedFields    = [
        'nom', 'prenom', 'password', 'role', 
        'departement_id', 'date_embauche', 'actif'
    ];

    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Recherche un employé actif par email.
     */
    public function findActiveByEmail(string $email): ?array
    {
        $email = trim(mb_strtolower($email));

        // On tente d'abord une colonne 'email' (recommandé). Si elle n'existe pas dans votre table,
        // adaptez ce champ (ex: 'username'/'login').
        $builder = $this->builder();

        // try/catch car certains SGBD peuvent lever une erreur si la colonne n'existe pas.
        try {
            $row = $this->where('LOWER(email)', $email)->where('actif', 1)->first();
            return $row ?: null;
        } catch (\Throwable $e) {
            // fallback: colonne alternative
            $row = $this->where('LOWER(username)', $email)->where('actif', 1)->first();
            return $row ?: null;
        }
    }

    /**
     * Vérifie le mot de passe.
     *
     * Compatible avec:
     * - hash `password_hash()` (recommandé)
     * - ancien stockage en clair (fallback)
     */
    public function verifyPassword(string $inputPassword, ?string $storedPassword): bool
    {
        $storedPassword = (string) ($storedPassword ?? '');
        if ($storedPassword === '') {
            return false;
        }

        if (str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$argon2')) {
            return password_verify($inputPassword, $storedPassword);
        }

        // fallback legacy: mot de passe en clair
        return hash_equals($storedPassword, $inputPassword);
    }
}
