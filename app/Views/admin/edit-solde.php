<?php
$solde = $solde ?? [];
$errors = session()->getFlashdata('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>TechMada RH — Modifier un solde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="/assets/css/main.css" rel="stylesheet" />
</head>

<body>
    <div class="app-wrap">
        <div class="main" style="margin-left:0;">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Modifier un solde</div>
                </div>
            </div>
            <div class="content">
                <?php if (!empty($errors)): ?><div class="flash flash-warn" style="margin-bottom:1rem"><?= esc(implode(' · ', $errors)) ?></div><?php endif; ?>
                <div class="form-section">
                    <div class="flash flash-info" style="margin-bottom:1rem">
                        <i class="bi bi-info-circle-fill"></i>
                        <span><?= esc(($solde['employe_nom'] ?? '') . ' ' . ($solde['employe_prenom'] ?? '')) ?> — <?= esc($solde['type_libelle'] ?? '') ?></span>
                    </div>
                    <form action="<?= base_url('admin/soldes/' . (int)($solde['id'] ?? 0) . '/edit') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-grid-2" style="margin-bottom:1rem">
                            <div class="f-group">
                                <label class="f-label">Année</label>
                                <input type="number" class="f-input" name="annee" value="<?= esc((string)($solde['annee'] ?? date('Y'))) ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Jours attribués</label>
                                <input type="number" step="0.5" class="f-input" name="jours_attribues" value="<?= esc((string)($solde['jours_attribues'] ?? 0)) ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Jours pris</label>
                                <input type="number" step="0.5" class="f-input" name="jours_pris" value="<?= esc((string)($solde['jours_pris'] ?? 0)) ?>" required />
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-forest">Enregistrer</button>
                            <a href="<?= base_url('admin/soldes') ?>" class="btn-secondary" style="text-decoration:none">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>