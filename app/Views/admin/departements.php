<?php
// Vue temporaire des départements
$departements = $departements ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Départements</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/main.css" />
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
        <li><a href="<?= base_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
        <li><a href="<?= base_url('admin/departements') ?>" class="active"><i class="bi bi-building"></i> Départements</a></li>
        <li><a href="<?= base_url('admin/types-conge') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
      </ul>
      <div class="sidebar-user">
        <div class="s-user-row">
          <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
          <div>
            <div class="user-name">Administrateur</div>
            <div class="user-role">Admin système</div>
          </div>
          <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </aside>

    <div class="main">
      <div class="topbar">
        <div>
          <div class="topbar-title">Départements</div>
          <div class="topbar-breadcrumb"><a href="<?= base_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Départements</div>
        </div>
      </div>

      <div class="content">
        <?php if ($msg = session()->getFlashdata('success')): ?>
          <div class="flash flash-success" style="margin-bottom:1rem"><i class="bi bi-check-circle-fill"></i> <?= esc($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
          <div class="flash flash-error" style="margin-bottom:1rem"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($msg) ?></div>
        <?php endif; ?>
        <div class="data-card">
          <div class="data-card-head">
            <h3>Liste des départements</h3>
          </div>
          <table class="tbl">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Employés</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($departements)): ?>
                <?php foreach ($departements as $dept): ?>
                  <tr>
                    <td><strong><?= esc($dept['nom'] ?? '') ?></strong></td>
                    <td><?= esc($dept['description'] ?? '') ?></td>
                    <td><?= $dept['employe_count'] ?? 0 ?></td>
                    <td>
                      <div class="action-btns">
                        <a class="btn-sm btn-edit" href="<?= base_url('admin/departements/' . (int)($dept['id'] ?? 0) . '/edit') ?>"><i class="bi bi-pencil"></i> Éditer</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align:center;padding:1rem;color:var(--muted)">Aucun département</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
    </div>
  </div>

</body>

</html>