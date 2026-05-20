<?php
$emp = $emp ?? [];
$departements = $departements ?? [];
$errors = session()->getFlashdata('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>TechMada RH — Modifier un employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/main.css" />
    <link rel="stylesheet" href="/assets/css/gestion-employes.css" />
</head>

<body>
    <div class="app-wrap">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
                <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
            </div>
            <ul class="sidebar-nav" style="margin-top:1rem">
                <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
                <li><a href="<?= base_url('admin/employes') ?>" class="active"><i class="bi bi-people"></i> Employés</a></li>
            </ul>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Modifier un employé</div>
                    <div class="topbar-breadcrumb"><a href="<?= base_url('admin/employes') ?>">Employés</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Édition</div>
                </div>
            </div>

            <div class="content">
                <div class="form-section">
                    <h3><i class="bi bi-pencil-square" style="color:var(--forest);margin-right:6px"></i>Modifier les informations</h3>

                    <?php if (!empty($errors)): ?>
                        <div class="flash flash-warn" style="margin-bottom:1rem">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>
                                <?= esc(implode(' · ', $errors)) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/employes/' . (int)($emp['id'] ?? 0) . '/edit') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-grid-2" style="margin-bottom:1rem">
                            <div class="f-group">
                                <label class="f-label">Prénom</label>
                                <input type="text" class="f-input" name="prenom" value="<?= esc($emp['prenom'] ?? '') ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Nom</label>
                                <input type="text" class="f-input" name="nom" value="<?= esc($emp['nom'] ?? '') ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Email</label>
                                <input type="email" class="f-input" name="email" value="<?= esc($emp['email'] ?? '') ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Nouveau mot de passe</label>
                                <input type="password" class="f-input" name="password" placeholder="Laisser vide pour conserver l'actuel" />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Département</label>
                                <select class="f-select" name="departement_id" required>
                                    <option value="">Sélectionner...</option>
                                    <?php foreach ($departements as $dept): ?>
                                        <option value="<?= (int)($dept['id'] ?? 0) ?>" <?= ((int)($emp['departement_id'] ?? 0) === (int)($dept['id'] ?? 0) ? 'selected' : '') ?>>
                                            <?= esc($dept['nom'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="f-group">
                                <label class="f-label">Rôle</label>
                                <select class="f-select" name="role" required>
                                    <option value="employe" <?= (($emp['role'] ?? '') === 'employe' ? 'selected' : '') ?>>Employé</option>
                                    <option value="rh" <?= (($emp['role'] ?? '') === 'rh' ? 'selected' : '') ?>>Responsable RH</option>
                                    <option value="admin" <?= (($emp['role'] ?? '') === 'admin' ? 'selected' : '') ?>>Administrateur</option>
                                </select>
                            </div>
                            <div class="f-group">
                                <label class="f-label">Date d'embauche</label>
                                <input type="date" class="f-input" name="date_embauche" value="<?= esc($emp['date_embauche'] ?? '') ?>" />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Statut</label>
                                <select class="f-select" name="actif">
                                    <option value="1" <?= ((int)($emp['actif'] ?? 1) === 1 ? 'selected' : '') ?>>Actif</option>
                                    <option value="0" <?= ((int)($emp['actif'] ?? 1) === 0 ? 'selected' : '') ?>>Inactif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-forest"><i class="bi bi-check2"></i> Enregistrer</button>
                            <a href="<?= base_url('admin/employes') ?>" class="btn-secondary" style="text-decoration:none">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>