<?php

/** @var array<string,mixed>|null $employe */
$employe = $employe ?? null;
$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
$flashSuccess = is_string($flashSuccess) ? $flashSuccess : '';
$flashError = is_string($flashError) ? $flashError : '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Mon profil</title>
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
        <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
      </div>
      <ul class="sidebar-nav" style="margin-top:1rem">
        <li><a href="<?= base_url('user/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
        <li><a href="<?= base_url('user/conges/nouveau') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
        <li><a href="<?= base_url('user/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
        <li><a href="/user/profil" class="active"><i class="bi bi-person"></i> Mon profil</a></li>
      </ul>
      <div class="sidebar-user">
        <?php $u = session()->get('user'); ?>
        <div class="s-user-row">
          <div class="avatar av-green">EMP</div>
          <div>
            <div class="user-name"><?= esc(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')) ?></div>
            <div class="user-role">Employé</div>
          </div>
          <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </aside>

    <div class="main">
      <div class="topbar">
        <div>
          <div class="topbar-title">Mon profil</div>
          <div class="topbar-breadcrumb"><a href="<?= base_url('user/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
        </div>
      </div>

      <div class="content">

        <?php if ($flashError !== ''): ?>
          <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($flashError) ?></div>
        <?php endif; ?>
        <?php if ($flashSuccess !== ''): ?>
          <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc($flashSuccess) ?></div>
        <?php endif; ?>

        <div class="form-section">
          <h3><i class="bi bi-person" style="color:var(--forest);margin-right:6px"></i>Informations</h3>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Prénom</label>
              <input class="f-input" value="<?= esc((string)($employe['prenom'] ?? '')) ?>" disabled />
            </div>
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input class="f-input" value="<?= esc((string)($employe['nom'] ?? '')) ?>" disabled />
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input class="f-input" value="<?= esc((string)($employe['email'] ?? '')) ?>" disabled />
            </div>
            <div class="f-group">
              <label class="f-label">Rôle</label>
              <input class="f-input" value="<?= esc((string)($employe['role'] ?? '')) ?>" disabled />
            </div>
          </div>
        </div>

        <div class="form-section">
          <h3><i class="bi bi-key" style="color:var(--forest);margin-right:6px"></i>Changer mon mot de passe</h3>
          <form method="post" action="/user/profil/password">
            <?= csrf_field() ?>
            <div class="f-group" style="max-width:420px">
              <label class="f-label">Nouveau mot de passe</label>
              <input type="password" class="f-input" name="password" required />
              <?php if (session('errors.password')): ?>
                <div class="f-error" style="display:block"><i class="bi bi-exclamation-circle"></i> <?= esc((string)session('errors.password')) ?></div>
              <?php endif; ?>
            </div>
            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-save"></i> Mettre à jour</button>
            </div>
          </form>
        </div>

      </div>
      <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
    </div>

  </div>
</body>

</html>