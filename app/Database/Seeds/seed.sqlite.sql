-- Données de test (SQLite)
-- Date: 2026-05-13
PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

-- Nettoyage (optionnel)
DELETE FROM conges;
DELETE FROM soldes;
DELETE FROM employees;
DELETE FROM types_conge;
DELETE FROM departements;

-- =========================
-- departements
-- =========================
INSERT INTO departements (id, nom, description) VALUES
  (1, 'Ressources Humaines', 'Gestion du personnel et des congés'),
  (2, 'Informatique', 'Développement et support'),
  (3, 'Finance', 'Comptabilité et contrôle de gestion');

-- =========================
-- employees
-- Note: "password" doit contenir un hash en vrai. Ici: valeurs de test.
-- =========================
INSERT INTO employees (id, nom, prenom, password, role, departement_id, date_embauche, actif, created_at, updated_at) VALUES
  (1, 'Admin', 'System', 'test123', 'admin', 2, '2023-01-10', 1, datetime('now'), datetime('now')),
  (2, 'Dupont', 'Marie', 'test123', 'rh', 1, '2023-03-15', 1, datetime('now'), datetime('now')),
  (3, 'Martin', 'Ali', 'test123', 'employe', 2, '2024-02-01', 1, datetime('now'), datetime('now')),
  (4, 'Nguyen', 'Sofia', 'test123', 'employe', 3, '2024-06-20', 1, datetime('now'), datetime('now'));

-- =========================
-- types_conge
-- deductible: 1 = oui, 0 = non
-- =========================
INSERT INTO types_conge (id, libelle, jours_annuels, deductible) VALUES
  (1, 'Congé payé', 30, 1),
  (2, 'Maladie', 0, 0),
  (3, 'Maternité/Paternité', 0, 0),
  (4, 'Sans solde', 0, 0);

-- =========================
-- soldes (par année)
-- =========================
INSERT INTO soldes (id, employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
  (1, 3, 1, 2026, 30, 5),
  (2, 4, 1, 2026, 30, 0),
  (3, 3, 2, 2026, 0, 2);

-- =========================
-- conges
-- statut: en_attente | approuve | refuse
-- traite_par: id employé RH / admin
-- =========================
INSERT INTO conges (id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, commentaire_rh, traite_par, created_at) VALUES
  (1, 3, 1, '2026-05-20', '2026-05-24', 5, 'Vacances', 'approuve', 'OK', 2, datetime('now')),
  (2, 4, 1, '2026-06-10', '2026-06-12', 3, 'Déplacement familial', 'en_attente', NULL, NULL, datetime('now')),
  (3, 3, 2, '2026-04-02', '2026-04-03', 2, 'Grippe', 'approuve', 'Certificat reçu', 2, datetime('now')),
  (4, 4, 4, '2026-07-01', '2026-07-05', 5, 'Raisons personnelles', 'refuse', 'Période chargée', 2, datetime('now'));

COMMIT;
