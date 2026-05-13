# TechMada RH - Système de Gestion des Congés

Application de gestion des congés pour une entreprise, développée avec CodeIgniter 4.

## Fonctionnalités

- **Espace Employé** : demande de congé, consultation des soldes, historique
- **Espace RH** : validation des demandes, gestion des soldes
- **Espace Admin** : dashboard, gestion des employés, départements, types de congé

## Prérequis

- PHP 8.1+
- Composer
- MySQL / PostgreSQL / SQLite
- Extension PHP PDO

## Installation

1. Cloner le dépôt :
```bash
git clone <repository-url>
cd departement
```

2. Installer les dépendances :
```bash
composer install
```

3. Configurer la base de données :
   - Copier `.env.example` vers `.env` (si existe) ou éditer `.env`
   - Définir les paramètres de connexion :
```
database.default.hostname = localhost
database.default.database = nom_base
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

4. Exécuter les migrations :
```bash
php spark migrate --all
```

5. (Optionnel) Insérer des données de test :
```bash
php spark db:seed TestDataSeeder
```

## Lancement du serveur

Démarrer le serveur de développement :

---
php -S localhost:8001 -t public
--

L'application sera accessible sur : **http://localhost:8001**

## Comptes de test

Après avoir exécuté le seeder :

- **Admin** : admin@techmada.mg / password123 (à créer manuellement ou via seeder)
- **RH** : rh@techmada.mg / password123
- **Employé** : soa@techmada.mg / password123

## Structure du projet

```
app/
  Config/       # Configuration (Routes, Filters, etc.)
  Controllers/  # Contrôleurs (Admin, RH, User)
  Models/       # Modèles (Employe, Conge, Solde, etc.)
  Views/        # Vues (admin/, RH/, user/)
  Database/     # Migrations et Seeds
  Helpers/      # Fonctions utilitaires
public/
  assets/       # CSS, JS, images
writable/
  database/     # Base de données SQLite (si utilisée)
```

## Routes principales

- `/login` - Connexion
- `/admin` - Dashboard admin
- `/admin/employes` - Gestion des employés
- `/admin/departements` - Gestion des départements
- `/rh` - Dashboard RH
- `/user/conges/nouveau` - Nouvelle demande de congé

## Notes

- L'application utilise le pattern MVC
- Les helpers personnalisés sont dans `app/Helpers/`
- Les vues admin sont rendues dynamiques avec des données réelles
