<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Soldes employés</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/assets/css/main.css"/>
<link rel="stylesheet" href="/assets/css/validation-rh.css"/>
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
      <li><a href="/rh/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/rh"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
      <li><a href="/rh/historique"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes" class="active"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <?php $u = session()->get('user'); ?>
      <div class="s-user-row">
        <div class="avatar av-blue">RH</div>
        <div>
          <div class="user-name"><?= esc(($u['prenom'] ?? '').' '.($u['nom'] ?? '')) ?></div>
          <div class="user-role">Responsable RH</div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Soldes employés</div>
        <div class="topbar-breadcrumb">Accueil <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes</div>
      </div>
    </div>

    <div class="content">

      <form method="get" action="/rh/soldes" style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <?php $annee = (int)($filters['annee'] ?? (int)date('Y')); ?>
        <input type="number" name="annee" class="f-input" value="<?= $annee ?>" style="width:120px;padding:6px 10px;font-size:.8rem" min="2000" max="2100"/>

        <select name="departement_id" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
          <option value="0">Tous les départements</option>
          <?php foreach (($departements ?? []) as $d): ?>
            <option value="<?= (int)($d['id'] ?? 0) ?>" <?= ((int)($filters['departement_id'] ?? 0) === (int)($d['id'] ?? 0)) ? 'selected' : '' ?>><?= esc((string)($d['nom'] ?? '')) ?></option>
          <?php endforeach; ?>
        </select>

        <button class="btn-forest" style="padding:7px 14px;font-size:.82rem" type="submit"><i class="bi bi-search"></i> Filtrer</button>
      </form>

      <div class="data-card">
        <div class="data-card-head"><h3>Soldes — <?= (int)($filters['annee'] ?? (int)date('Y')) ?></h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Type</th><th>Attribués</th><th>Pris</th><th>Restant</th></tr>
          </thead>
          <tbody>
          <?php if (empty($soldes)): ?>
            <tr><td colspan="6" class="td-muted" style="padding:1rem">Aucun solde.</td></tr>
          <?php else: ?>
            <?php foreach ($soldes as $s): ?>
              <?php
                $attrib = (int)($s['jours_attribues'] ?? 0);
                $pris = (int)($s['jours_pris'] ?? 0);
                $rest = max(0, $attrib - $pris);
              ?>
              <tr>
                <td><?= esc(($s['prenom'] ?? '').' '.($s['nom'] ?? '')) ?></td>
                <td class="td-muted"><?= esc((string)($s['departement_nom'] ?? '')) ?></td>
                <td><?= esc((string)($s['type_libelle'] ?? '')) ?></td>
                <td class="td-mono"><?= $attrib ?></td>
                <td class="td-mono"><?= $pris ?></td>
                <td class="td-mono" style="color:var(--forest);font-weight:600"><?= $rest ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</body>
</html>
