<?php
$dept = $dept ?? [];
$errors = session()->getFlashdata('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>TechMada RH — Modifier un département</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="/assets/css/main.css" rel="stylesheet" />
</head>

<body>
    <div class="app-wrap">
        <div class="main" style="margin-left:0;">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Modifier un département</div>
                </div>
            </div>
            <div class="content">
                <?php if (!empty($errors)): ?><div class="flash flash-warn" style="margin-bottom:1rem"><?= esc(implode(' · ', $errors)) ?></div><?php endif; ?>
                <div class="form-section">
                    <form action="<?= base_url('admin/departements/' . (int)($dept['id'] ?? 0) . '/edit') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-grid-2" style="margin-bottom:1rem">
                            <div class="f-group">
                                <label class="f-label">Nom</label>
                                <input type="text" class="f-input" name="nom" value="<?= esc($dept['nom'] ?? '') ?>" required />
                            </div>
                            <div class="f-group">
                                <label class="f-label">Description</label>
                                <textarea class="f-input" name="description" rows="4"><?= esc($dept['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-forest">Enregistrer</button>
                            <a href="<?= base_url('admin/departements') ?>" class="btn-secondary" style="text-decoration:none">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>