<?php

/** @var array<int, array<string, mixed>> $demandes */
$stats = $stats ?? [];
$typeStats = $typeStats ?? [];
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Mes demandes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/main.css" />
  <link rel="stylesheet" href="/assets/css/mes-demandes.css" />
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
        <li><a href="<?= base_url('user/calendrier') ?>"><i class="bi bi-calendar-week"></i> Vue calendrier</a></li>
        <li><a href="<?= base_url('user/conges/nouveau') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
        <li><a href="<?= base_url('user/conges') ?>" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
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
          <div class="topbar-title">Historique &amp; statistiques</div>
          <div class="topbar-breadcrumb"><a href="<?= base_url('user/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
        </div>
        <div class="topbar-actions">
          <a href="<?= base_url('user/conges/nouveau') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
        </div>
      </div>

      <div class="content">

        <div class="metrics" style="margin-bottom:1.25rem">
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-forest"><i class="bi bi-list-check"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['total'] ?? 0) ?></div>
            <div class="metric-label">Total demandes</div>
          </div>
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
              <div class="metric-icon mi-blue"><i class="bi bi-people"></i></div>
            </div>
            <div class="metric-val"><?= (int)($stats['jours_total'] ?? 0) ?></div>
            <div class="metric-label">Jours demandés</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;margin-bottom:1.25rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3>Répartition par type</h3>
            </div>
            <div style="padding:1rem;display:flex;flex-direction:column;gap:.8rem">
              <?php if (empty($typeStats)): ?>
                <div class="td-muted">Aucune statistique disponible.</div>
              <?php else: ?>
                <?php $maxType = max($typeStats); ?>
                <?php foreach ($typeStats as $label => $count): ?>
                  <?php $pct = $maxType > 0 ? (int) round(($count / $maxType) * 100) : 0; ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:4px">
                      <span><?= esc($label) ?></span><strong><?= (int)$count ?></strong>
                    </div>
                    <div class="solde-bar">
                      <div class="solde-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0;height:100%">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Cette page se met à jour automatiquement selon vos demandes enregistrées dans la base.</span>
          </div>
        </div>

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
            <form method="get" action="<?= base_url('user/conges') ?>" style="display:flex;gap:6px">
              <select class="f-select" name="statut" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
                <option value="" <?= $statut === '' ? 'selected' : ''; ?>>Tous les statuts</option>
                <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                <option value="approuvee" <?= $statut === 'approuvee' ? 'selected' : ''; ?>>Approuvée</option>
                <option value="refusee" <?= $statut === 'refusee' ? 'selected' : ''; ?>>Refusée</option>
                <option value="annulee" <?= $statut === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
              </select>
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
                  $id = (int) ($d['id'] ?? 0);
                  $s = (string) ($d['statut'] ?? '');
                  $badge = $s === 'approuvee' ? 's-approuvee' : ($s === 'refusee' ? 's-refusee' : ($s === 'annulee' ? 's-annulee' : 's-attente'));
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
                        <form method="post" action="<?= base_url('user/conges/' . $id . '/cancel') ?>" onsubmit="return confirm('Annuler cette demande ?');" style="display:inline">
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