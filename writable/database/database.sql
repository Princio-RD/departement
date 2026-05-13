-- Script SQLite (généré depuis les Models CI4)
-- Date: 2026-05-13
-- Remarque: exécuter avec PRAGMA foreign_keys=ON;

PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

-- =========================
-- Table: departements
-- =========================
CREATE TABLE IF NOT EXISTS departements (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  description TEXT
);

CREATE INDEX IF NOT EXISTS idx_departements_nom ON departements(nom);

-- =========================
-- Table: employees
-- =========================
CREATE TABLE IF NOT EXISTS employees (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  prenom TEXT NOT NULL,
  email TEXT NOT NULL,
  password TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'employe',
  departement_id INTEGER,
  date_embauche TEXT,
  actif INTEGER NOT NULL DEFAULT 1,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY (departement_id) REFERENCES departements(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_employees_email ON employees(email);

-- =========================
-- Table: types_conge
-- =========================
CREATE TABLE IF NOT EXISTS types_conge (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  libelle TEXT NOT NULL,
  jours_annuels INTEGER NOT NULL DEFAULT 0,
  deductible INTEGER NOT NULL DEFAULT 1
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_types_conge_libelle ON types_conge(libelle);

-- =========================
-- Table: soldes
-- =========================
CREATE TABLE IF NOT EXISTS soldes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id INTEGER NOT NULL,
  type_conge_id INTEGER NOT NULL,
  annee INTEGER NOT NULL,
  jours_attribues INTEGER NOT NULL DEFAULT 0,
  jours_pris INTEGER NOT NULL DEFAULT 0,
  FOREIGN KEY (employe_id) REFERENCES employees(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (type_conge_id) REFERENCES types_conge(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_soldes_emp_type_annee ON soldes(employe_id, type_conge_id, annee);
CREATE INDEX IF NOT EXISTS idx_soldes_employe_id ON soldes(employe_id);
CREATE INDEX IF NOT EXISTS idx_soldes_type_conge_id ON soldes(type_conge_id);

-- =========================
-- Table: conges
-- =========================
CREATE TABLE IF NOT EXISTS conges (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id INTEGER NOT NULL,
  type_conge_id INTEGER NOT NULL,
  date_debut TEXT NOT NULL,
  date_fin TEXT NOT NULL,
  nb_jours INTEGER NOT NULL DEFAULT 0,
  motif TEXT,
  statut TEXT NOT NULL DEFAULT 'en_attente',
  commentaire_rh TEXT,
  traite_par INTEGER,
  created_at TEXT,
  FOREIGN KEY (employe_id) REFERENCES employees(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (type_conge_id) REFERENCES types_conge(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  FOREIGN KEY (traite_par) REFERENCES employees(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_conges_employe_id ON conges(employe_id);
CREATE INDEX IF NOT EXISTS idx_conges_type_conge_id ON conges(type_conge_id);
CREATE INDEX IF NOT EXISTS idx_conges_statut ON conges(statut);
CREATE INDEX IF NOT EXISTS idx_conges_date_debut ON conges(date_debut);


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
INSERT INTO employees (id, nom, prenom, email, password, role, departement_id, date_embauche, actif, created_at, updated_at) VALUES
  (1, 'Admin', 'System', 'admin@techmada.mg', 'admin123', 'admin', 2, '2023-01-10', 1, datetime('now'), datetime('now')),
  (2, 'Dupont', 'Marie', 'rh@techmada.mg', 'rh123', 'rh', 1, '2023-03-15', 1, datetime('now'), datetime('now')),
  (3, 'Martin', 'Ali', 'employe@techmada.mg', 'emp123', 'employe', 2, '2024-02-01', 1, datetime('now'), datetime('now')),
  (4, 'Nguyen', 'Sofia', 'sofia@techmada.mg', 'emp123', 'employe', 3, '2024-06-20', 1, datetime('now'), datetime('now'));

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
-- statut: en_attente | approuvee | refusee
-- traite_par: id employé RH / admin
-- =========================
INSERT INTO conges (id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, commentaire_rh, traite_par, created_at) VALUES
  (1, 3, 1, '2026-05-20', '2026-05-24', 5, 'Vacances', 'approuvee', 'OK', 2, datetime('now')),
  (2, 4, 1, '2026-06-10', '2026-06-12', 3, 'Déplacement familial', 'en_attente', NULL, NULL, datetime('now')),
  (3, 3, 2, '2026-04-02', '2026-04-03', 2, 'Grippe', 'approuvee', 'Certificat reçu', 2, datetime('now')),
  (4, 4, 4, '2026-07-01', '2026-07-05', 5, 'Raisons personnelles', 'refusee', 'Période chargée', 2, datetime('now'));

COMMIT;
