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

CREATE INDEX IF NOT EXISTS idx_employees_departement_id ON employees(departement_id);
CREATE INDEX IF NOT EXISTS idx_employees_role ON employees(role);
CREATE INDEX IF NOT EXISTS idx_employees_actif ON employees(actif);

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

COMMIT;
