<?php
/** @var array<int, array<string, mixed>> $demandes */
$demandes = $demandes ?? [];
$filters = $filters ?? [];

$statut = (string) ($filters['statut'] ?? '');

$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
$flashSuccess = is_string($flashSuccess) ? $flashSuccess : '';
$flashError = is_string($flashError) ? $flashError : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Mes demandes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/assets/css/main.css"/>
<link rel="stylesheet" href="/assets/css/mes-demandes.css"/>
</head>
<body>

<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/user/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/user/conges/nouveau"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/user/conges" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/user/profil"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <?php $u = session()->get('user'); ?>
      <div class="s-user-row">
        <div class="avatar av-green">EMP</div>
        <div>
          <div class="user-name"><?= esc(($u['prenom'] ?? '').' '.($u['nom'] ?? '')) ?></div>
          <div class="user-role">Employé</div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="/user/dashboard">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="/user/conges/nouveau" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">

      <?php if ($flashError !== ''): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc($flashError) ?>
        </div>
      <?php endif; ?>

      <?php if ($flashSuccess !== ''): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc($flashSuccess) ?>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <form method="get" action="/user/conges" style="display:flex;gap:6px">
            <select class="f-select" name="statut" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
              <option value="" <?= $statut===''?'selected':''; ?>>Tous les statuts</option>
              <option value="en_attente" <?= $statut==='en_attente'?'selected':''; ?>>En attente</option>
              <option value="approuvee" <?= $statut==='approuvee'?'selected':''; ?>>Approuvée</option>
              <option value="refusee" <?= $statut==='refusee'?'selected':''; ?>>Refusée</option>
              <option value="annulee" <?= $statut==='annulee'?'selected':''; ?>>Annulée</option>
            </select>
          </form>
        </div>

        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if (empty($demandes)): ?>
            <tr><td colspan="7" class="td-muted" style="padding:1rem">Aucune demande.</td></tr>
          <?php else: ?>
            <?php foreach ($demandes as $d): ?>
              <?php
                $id = (int) ($d['id'] ?? 0);
                $s = (string) ($d['statut'] ?? '');
                $badge = $s==='approuvee' ? 's-approuvee' : ($s==='refusee' ? 's-refusee' : ($s==='annulee' ? 's-annulee' : 's-attente'));
              ?>
              <tr>
                <td><span class="type-badge t-annuel"><?= esc((string)($d['type_libelle'] ?? '')) ?></span></td>
                <td class="td-muted td-mono"><?= esc((string)($d['date_debut'] ?? '')) ?></td>
                <td class="td-muted td-mono"><?= esc((string)($d['date_fin'] ?? '')) ?></td>
                <td class="td-mono"><?= (int)($d['nb_jours'] ?? 0) ?> j</td>
                <td><span class="statut <?= $badge ?>"><?= esc($s) ?></span></td>
                <td class="td-muted" style="font-size:.78rem"><?= esc((string)($d['commentaire_rh'] ?? '—')) ?></td>
                <td>
                  <?php if ($s === 'en_attente'): ?>
                    <form method="post" action="/user/conges/<?= $id ?>/cancel" onsubmit="return confirm('Annuler cette demande ?');" style="display:inline">
                      <?= csrf_field() ?>
                      <button class="btn-sm btn-cancel" type="submit"><i class="bi bi-x"></i> Annuler</button>
                    </form>
                  <?php else: ?>
                    <span class="td-muted" style="font-size:.75rem">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
  </div>

</div>

</body>
</html>
