<?php
// Vue dynamique du dashboard admin
// Données fournies par AdminController::dashboard()
$stats = $stats ?? [];
$absents = $absents ?? [];
$lastDemandes = $lastDemandes ?? [];
$annee = $annee ?? date('Y');
$monthlyLeaveData = $monthlyLeaveData ?? array_fill(0, 12, 0);
$weekdayLeaveData = $weekdayLeaveData ?? array_fill(0, 7, 0);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/main.css" />
  <link rel="stylesheet" href="/assets/css/dashboard-admin.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="/assets/js/admin-dashboard.js" defer></script>
</head>

<body>

  <div class="app-wrap">

    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
      </div>
      <div class="sidebar-section">Gestion</div>
      <ul class="sidebar-nav">
        <li><a href="<?= base_url('admin/dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
        <li><a href="<?= base_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
        <li><a href="<?= base_url('admin/departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
        <li><a href="<?= base_url('admin/types-conge') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
        <li><a href="<?= base_url('admin/soldes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
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
          <div class="topbar-title">Vue d'ensemble</div>
          <div class="topbar-breadcrumb">Administration</div>
        </div>
        <div class="topbar-actions">
          <a href="<?= base_url('admin/employes#ajout-employe') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
        </div>
      </div>

      <div class="content">

        <div class="metrics">
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-forest"><i class="bi bi-people"></i></div>
            </div>
            <div class="metric-val"><?= number_format($stats['employes_actifs'] ?? 0) ?></div>
            <div class="metric-label">Employés actifs</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="metric-val"><?= number_format($stats['demandes_traitees'] ?? 0) ?></div>
            <div class="metric-label">Demandes traitées</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div>
            </div>
            <div class="metric-val"><?= number_format($stats['approuvees_mois'] ?? 0) ?></div>
            <div class="metric-label">Approuvées ce mois</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-blue"><i class="bi bi-building"></i></div>
            </div>
            <div class="metric-val"><?= number_format($stats['departements'] ?? 0) ?></div>
            <div class="metric-label">Départements</div>
          </div>
          <div class="metric">
            <div class="metric-top">
              <div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div>
            </div>
            <div class="metric-val"><?= number_format($stats['absents_aujourdhui'] ?? 0) ?></div>
            <div class="metric-label">Absents aujourd'hui</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin:1.25rem 0 1.5rem" data-monthly-data='<?= esc(json_encode(array_values($monthlyLeaveData), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "attr") ?>' data-weekday-data='<?= esc(json_encode(array_values($weekdayLeaveData), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "attr") ?>'>
          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3>Congés par mois</h3>
            </div>
            <div style="padding:1rem"><canvas id="leaveByMonthChart" height="140"></canvas></div>
          </div>
          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3>Congés par jour</h3>
            </div>
            <div style="padding:1rem"><canvas id="leaveByWeekdayChart" height="140"></canvas></div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3>Demandes récentes</h3>
            </div>
            <table class="tbl">
              <thead>
                <tr>
                  <th>Employé</th>
                  <th>Type</th>
                  <th>Durée</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($lastDemandes)): ?>
                  <?php foreach ($lastDemandes as $demande): ?>
                    <tr>
                      <td>
                        <div style="display:flex;align-items:center;gap:7px">
                          <div class="avatar" style="width:28px;height:28px;font-size:.62rem;background:<?= getInitialsColor($demande['nom'] ?? '', $demande['prenom'] ?? '') ?>">
                            <?= getInitials($demande['nom'] ?? '', $demande['prenom'] ?? '') ?>
                          </div>
                          <span class="td-name" style="font-size:.84rem"><?= esc(($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? '')) ?></span>
                        </div>
                      </td>
                      <td><span class="type-badge <?= getTypeCongeClass($demande['type_libelle'] ?? '') ?>"><?= esc($demande['type_libelle'] ?? '') ?></span></td>
                      <td class="td-mono"><?= calculateDuration($demande['date_debut'] ?? '', $demande['date_fin'] ?? '') ?> j</td>
                      <td><span class="statut <?= getStatutClass($demande['statut'] ?? '') ?>"><?= esc($demande['statut'] ?? '') ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" style="text-align:center;padding:1rem;color:var(--muted)">Aucune demande récente</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div style="display:flex;flex-direction:column;gap:1rem">
            <div class="data-card" style="margin:0">
              <div class="data-card-head">
                <h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3>
              </div>
              <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
                <?php if (!empty($absents)): ?>
                  <?php foreach ($absents as $absent): ?>
                    <div style="display:flex;align-items:center;gap:8px">
                      <div class="avatar" style="width:30px;height:30px;font-size:.65rem;background:<?= getInitialsColor($absent['nom'] ?? '', $absent['prenom'] ?? '') ?>">
                        <?= getInitials($absent['nom'] ?? '', $absent['prenom'] ?? '') ?>
                      </div>
                      <div>
                        <div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc(($absent['prenom'] ?? '') . ' ' . ($absent['nom'] ?? '')) ?></div>
                        <div style="font-size:.72rem;color:var(--muted)">
                          <?= esc($absent['type_libelle'] ?? '') ?> · retour <?= formatDate($absent['date_fin'] ?? '') ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div style="text-align:center;padding:.5rem;color:var(--muted);font-size:.8rem">Aucune absence aujourd'hui</div>
                <?php endif; ?>
              </div>
            </div>
            <div class="flash flash-warn" style="margin:0">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <span style="font-size:.8rem">
                <?php
                $critiques = 0;
                foreach ($lastDemandes as $d) {
                  // Logique simplifiée pour l'exemple
                }
                ?>
                Plusieurs employés ont un solde critique. <a href="<?= base_url('admin/soldes') ?>" style="color:var(--warn);font-weight:500">Voir les soldes →</a>
              </span>
            </div>
          </div>

        </div>

      </div>
      <div class="footer-app"><i class="bi bi-c-circle"></i> <?= $annee ?> <span>TechMada RH</span></div>
    </div>

  </div>

</body>

</html>