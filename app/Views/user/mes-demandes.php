<?php
/** @var array<int, array<string, mixed>> $demandes */
/** @var string $statut */

$demandes = $demandes ?? [];
$statut   = is_string($statut ?? '') ? (string) $statut : '';

$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');
$flashSuccess = is_string($flashSuccess) ? $flashSuccess : '';
$flashError   = is_string($flashError) ? $flashError : '';

function statut_badge_class(string $s): string
{
    return match ($s) {
        'en_attente' => 's-attente',
        'approuvee'  => 's-approuvee',
        'refusee'    => 's-refusee',
        'annulee'    => 's-annulee',
        default      => 's-attente',
    };
}

function statut_label(string $s): string
{
    return match ($s) {
        'en_attente' => 'en attente',
        'approuvee'  => 'approuvée',
        'refusee'    => 'refusée',
        'annulee'    => 'annulée',
        default      => $s,
    };
}

function fmt_date(?string $ymd): string
{
    if (! is_string($ymd) || $ymd === '') {
        return '—';
    }
    $ts = strtotime($ymd);
    if ($ts === false) {
        return esc($ymd);
    }
    return date('d/m/Y', $ts);
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Mes demandes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/css/main.css"/>
<link rel="stylesheet" href="/css/mes-demandes.css"/>
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
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div><div class="user-name">Soa Rakoto</div><div class="user-role">Employé · IT</div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb">
          <a href="/user/dashboard">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes
        </div>
      </div>
      <div class="topbar-actions">
        <a href="/user/conges/nouveau" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">

      <?php if ($flashSuccess !== ''): ?>
        <div class="flash flash-success" style="margin:0 0 1rem 0">
          <i class="bi bi-check-circle-fill"></i>
          <span style="font-size:.85rem"><?= esc($flashSuccess) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($flashError !== ''): ?>
        <div class="flash flash-danger" style="margin:0 0 1rem 0">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span style="font-size:.85rem"><?= esc($flashError) ?></span>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>

          <form method="get" action="/user/conges" style="display:flex;gap:6px;align-items:center">
            <select name="statut" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
              <option value="" <?= $statut === '' ? 'selected' : '' ?>>Tous les statuts</option>
              <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
              <option value="approuvee" <?= $statut === 'approuvee' ? 'selected' : '' ?>>Approuvée</option>
              <option value="refusee" <?= $statut === 'refusee' ? 'selected' : '' ?>>Refusée</option>
              <option value="annulee" <?= $statut === 'annulee' ? 'selected' : '' ?>>Annulée</option>
            </select>
            <noscript>
              <button class="btn-forest" type="submit" style="padding:7px 14px;font-size:.82rem">Filtrer</button>
            </noscript>
          </form>
        </div>

        <table class="tbl">
          <thead>
            <tr>
              <th>Type</th>
              <th>Début</th>
              <th>Fin</th>
              <th>Durée</th>
              <th>Statut</th>
              <th>Commentaire RH</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($demandes)): ?>
              <tr>
                <td colspan="7" class="td-muted" style="padding:1rem">Aucune demande.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($demandes as $d): ?>
                <?php
                  $id         = (int) ($d['id'] ?? 0);
                  $typeLib    = isset($d['type_libelle']) && is_string($d['type_libelle']) ? $d['type_libelle'] : '';
                  $deb        = isset($d['date_debut']) && is_string($d['date_debut']) ? $d['date_debut'] : null;
                  $fin        = isset($d['date_fin']) && is_string($d['date_fin']) ? $d['date_fin'] : null;
                  $nbJours    = (int) ($d['nb_jours'] ?? 0);
                  $st         = isset($d['statut']) && is_string($d['statut']) ? $d['statut'] : '';
                  $comm       = isset($d['commentaire_rh']) && is_string($d['commentaire_rh']) ? $d['commentaire_rh'] : '';
                  $canCancel  = ($st === 'en_attente');
                ?>
                <tr>
                  <td><?= esc($typeLib) ?></td>
                  <td class="td-muted"><?= esc(fmt_date($deb)) ?></td>
                  <td class="td-muted"><?= esc(fmt_date($fin)) ?></td>
                  <td class="td-mono"><?= $nbJours ?> j</td>
                  <td><span class="statut <?= esc(statut_badge_class($st)) ?>"><?= esc(statut_label($st)) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem"><?= $comm !== '' ? esc($comm) : '—' ?></td>
                  <td>
                    <?php if ($canCancel && $id > 0): ?>
                      <form method="post" action="/user/conges/<?= $id ?>/annuler" onsubmit="return confirm('Annuler cette demande ?')" style="display:inline">
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
