<?php

/** @var array<string,int> $stats */
/** @var array<int, array<string,mixed>> $soldes */
/** @var array<int, array<string,mixed>> $lastDemandes */

$stats = $stats ?? [];
$soldes = $soldes ?? [];
$lastDemandes = $lastDemandes ?? [];
$annee = (int) ($annee ?? (int)date('Y'));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Tableau de bord</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/main.css" />
  <link rel="stylesheet" href="/assets/css/dashboard-employe.css" />
</head>

<body>

  <div class="app-wrap">

    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
      </div>
      <ul class="sidebar-nav" style="margin-top:1rem">
        <li><a href="<?= base_url('user/dashboard') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
        <li><a href="<?= base_url('user/calendrier') ?>"><i class="bi bi-calendar-week"></i> Vue calendrier</a></li>
        <li><a href="<?= base_url('user/conges/nouveau') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
        <li><a href="<?= base_url('user/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
        <li><a href="<?= base_url('user/profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
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
          <div class="topbar-title">Tableau de bord</div>
          <div class="topbar-breadcrumb">Accueil</div>
        </div>
        <div class="topbar-actions">
          <a href="<?= base_url('user/conges/nouveau') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
            <i class="bi bi-plus-circle"></i> Nouvelle demande
          </a>
        </div>
      </div>

      <div class="content">

        <div class="metrics">
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['en_attente'] ?? 0) ?></div>
            <div class="metric-label">En attente</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['approuvee'] ?? 0) ?></div>
            <div class="metric-label">Approuvées</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['refusee'] ?? 0) ?></div>
            <div class="metric-label">Refusées</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-blue"><i class="bi bi-x-octagon"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['annulee'] ?? 0) ?></div>
            <div class="metric-label">Annulées</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">

          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3>Dernières demandes</h3>
              <a href="<?= base_url('user/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
            </div>
            <table class="tbl">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Période</th>
                  <th>Durée</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($lastDemandes)): ?>
                  <tr>
                    <td colspan="4" class="td-muted" style="padding:1rem">Aucune demande.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($lastDemandes as $d): ?>
                    <?php
                    $s = (string) ($d['statut'] ?? '');
                    $badge = $s === 'approuvee' ? 's-approuvee' : ($s === 'refusee' ? 's-refusee' : ($s === 'annulee' ? 's-annulee' : 's-attente'));
                    ?>
                    <tr>
                      <td><?= esc((string)($d['type_libelle'] ?? '')) ?></td>
                      <td class="td-mono"><?= esc((string)($d['date_debut'] ?? '')) ?> → <?= esc((string)($d['date_fin'] ?? '')) ?></td>
                      <td class="td-mono"><?= (int)($d['nb_jours'] ?? 0) ?> j</td>
                      <td><span class="statut <?= $badge ?>"><?= esc($s) ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Mes soldes — <?= $annee ?></h3>
            </div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (empty($soldes)): ?>
                <div style="font-size:.8rem;color:var(--muted)">Aucun solde disponible.</div>
              <?php else: ?>
                <?php foreach ($soldes as $s): ?>
                  <?php
                  $restant = max(0, (int) ($s['jours_attribues'] ?? 0) - (int) ($s['jours_pris'] ?? 0));
                  $lib = (string) ($s['libelle'] ?? '');
                  $attrib = max(1, (int) ($s['jours_attribues'] ?? 0));
                  $pct = (int) min(100, round(($restant / $attrib) * 100));
                  ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:.8rem;color:var(--ink)"><?= esc($lib) ?></span>
                      <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= $restant ?> j</span>
                    </div>
                    <div class="solde-bar">
                      <div class="solde-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div>

      </div>
      <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
    </div>

  </div>

</body>

</html>