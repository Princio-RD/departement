<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Tableau de bord RH</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/assets/css/main.css"/>
<link rel="stylesheet" href="/assets/css/dashboard-employe.css"/>
</head>
<body>

<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/rh/dashboard" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/rh"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
      <li><a href="/rh/historique"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <?php $u = session()->get('user'); ?>
      <div class="s-user-row">
        <div class="avatar av-blue">RH</div>
        <div>
          <div class="user-name"><?= esc((string)(($u['prenom'] ?? '').' '.($u['nom'] ?? ''))) ?></div>
          <div class="user-role">Responsable RH</div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord RH</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="/rh" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-inbox"></i> Voir les demandes
        </a>
      </div>
    </div>

    <div class="content">

      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= (int)($stats['en_attente'] ?? 0) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= (int)($stats['approuvee'] ?? 0) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= (int)($stats['refusee'] ?? 0) ?></div>
          <div class="metric-label">Refusées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar2-week"></i></div></div>
          <div class="metric-val"><?= (int)($annee ?? (int)date('Y')) ?></div>
          <div class="metric-label">Année</div>
        </div>
      </div>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Dernières demandes</h3>
          <a href="/rh" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th></tr>
          </thead>
          <tbody>
            <?php if (empty($lastDemandes)): ?>
              <tr><td colspan="6" class="td-muted" style="padding:1rem">Aucune demande.</td></tr>
            <?php else: ?>
              <?php foreach ($lastDemandes as $d): ?>
                <?php
                  $typeLib = is_string($d['type_libelle'] ?? null) ? $d['type_libelle'] : '';
                  $dd = is_string($d['date_debut'] ?? null) ? $d['date_debut'] : '';
                  $df = is_string($d['date_fin'] ?? null) ? $d['date_fin'] : '';
                  $stat = (string)($d['statut'] ?? '');
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">EMP</div>
                      <div class="profile-info">
                        <div class="pname"><?= esc((string)(($d['prenom'] ?? '').' '.($d['nom'] ?? ''))) ?></div>
                        <?php $deptNom = is_string($d['departement_nom'] ?? null) ? $d['departement_nom'] : ''; ?>
                        <div class="pdept"><?= esc((string)$deptNom) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge"><?= esc((string)$typeLib) ?></span></td>
                  <td class="td-muted"><?= esc((string)$dd) ?></td>
                  <td class="td-muted"><?= esc((string)$df) ?></td>
                  <td class="td-mono"><?= (int)($d['nb_jours'] ?? 0) ?> j</td>
                  <td>
                    <?php if ($stat === 'en_attente'): ?>
                      <span class="statut s-attente">en attente</span>
                    <?php elseif ($stat === 'approuvee'): ?>
                      <span class="statut s-approuvee">approuvée</span>
                    <?php elseif ($stat === 'refusee'): ?>
                      <span class="statut s-refusee">refusée</span>
                    <?php else: ?>
                      <span class="statut"><?= esc((string)$stat) ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Soldes (top) — <?= (int)($annee ?? (int)date('Y')) ?></h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Attribués</th><th>Pris</th><th>Restant</th></tr>
          </thead>
          <tbody>
            <?php if (empty($soldes)): ?>
              <tr><td colspan="5" class="td-muted" style="padding:1rem">Aucun solde.</td></tr>
            <?php else: ?>
              <?php foreach ($soldes as $s): ?>
                <?php
                  $typeLib = is_string($s['type_libelle'] ?? null) ? $s['type_libelle'] : '';
                  $attrib = (int)($s['jours_attribues'] ?? 0);
                  $pris = (int)($s['jours_pris'] ?? 0);
                  $rest = max(0, $attrib - $pris);
                ?>
                <tr>
                  <td><?= esc((string)(($s['prenom'] ?? '').' '.($s['nom'] ?? ''))) ?></td>
                  <td><span class="type-badge"><?= esc((string)$typeLib) ?></span></td>
                  <td class="td-mono"><?= $attrib ?></td>
                  <td class="td-mono"><?= $pris ?></td>
                  <td class="td-mono" style="color:var(--success);font-weight:600">
                    <?= $rest ?>
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
