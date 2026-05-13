<?php
/**
 * Admin Helper
 * Fonctions utilitaires pour les vues admin
 */

if (!function_exists('getInitials')) {
    /**
     * Retourne les initiales d'un nom et prénom
     */
    function getInitials(string $nom, string $prenom): string
    {
        $nom = trim($nom);
        $prenom = trim($prenom);

        if ($nom === '' && $prenom === '') {
            return '??';
        }

        $initialNom = mb_substr($nom, 0, 1, 'UTF-8');
        $initialPrenom = mb_substr($prenom, 0, 1, 'UTF-8');

        return strtoupper($initialPrenom . $initialNom);
    }
}

if (!function_exists('getInitialsColor')) {
    /**
     * Retourne une couleur de fond pour l'avatar basée sur le nom
     */
    function getInitialsColor(string $nom, string $prenom): string
    {
        $couleurs = [
            '#5a2d82', // violet (admin)
            '#2d5a82', // bleu
            '#2d8280', // vert foncé
            '#5a8230', // vert
            '#827a2d', // jaune
            '#825c2d', // orange
            '#822d2d', // rouge
        ];

        $complet = $nom . $prenom;
        if ($complet === '') {
            return '#666';
        }

        $index = array_sum(array_map('ord', str_split($complet))) % count($couleurs);
        return $couleurs[$index];
    }
}

if (!function_exists('getTypeCongeClass')) {
    /**
     * Retourne la classe CSS pour un type de congé
     */
    function getTypeCongeClass(string $type): string
    {
        $type = strtolower(trim($type));

        return match ($type) {
            'annuel' => 't-annuel',
            'maladie' => 't-maladie',
            'maternité', 'maternel' => 't-maternite',
            'paternité', 'paternel' => 't-paternite',
            'spécial', 'special' => 't-special',
            default => 't-annuel',
        };
    }
}

if (!function_exists('getStatutClass')) {
    /**
     * Retourne la classe CSS pour un statut de congé
     */
    function getStatutClass(string $statut): string
    {
        $statut = strtolower(trim($statut));

        return match ($statut) {
            'approuvée', 'approuvee' => 's-approuvee',
            'en attente', 'en_attente' => 's-attente',
            'rejetée', 'rejetee' => 's-rejetee',
            'annulée', 'annulee' => 's-annulee',
            default => 's-attente',
        };
    }
}

if (!function_exists('calculateDuration')) {
    /**
     * Calcule la durée en jours entre deux dates
     */
    function calculateDuration(string $dateDebut, string $dateFin): int
    {
        try {
            $debut = new DateTime($dateDebut);
            $fin = new DateTime($dateFin);
            $interval = $debut->diff($fin);
            return (int) $interval->days + 1; // +1 car les deux dates sont incluses
        } catch (Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formate une date pour l'affichage
     */
    function formatDate(string $date): string
    {
        try {
            $dt = new DateTime($date);
            return $dt->format('d/m/Y');
        } catch (Exception $e) {
            return $date;
        }
    }
}

if (!function_exists('getRoleColor')) {
    /**
     * Retourne la couleur de fond pour un rôle
     */
    function getRoleColor(string $role): string
    {
        return match (strtolower($role)) {
            'admin' => '#5a2d82',
            'rh' => '#2d5a82',
            default => '#f1efe8', // gris clair pour employé
        };
    }
}

if (!function_exists('getRoleTextColor')) {
    /**
     * Retourne la couleur du texte pour un rôle
     */
    function getRoleTextColor(string $role): string
    {
        return match (strtolower($role)) {
            'admin' => '#fff',
            'rh' => '#fff',
            default => '#444441',
        };
    }
}
