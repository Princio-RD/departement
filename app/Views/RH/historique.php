<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Historique</title>
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
      <li><a href="/rh/historique" class="active"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes"><i class="bi bi-people"></i> Soldes employés</a></li>
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
        <div class="topbar-title">Historique des demandes</div>
        <div class="topbar-breadcrumb">Accueil <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Historique</div>
      </div>
    </div>

    <div class="content">

      <form method="get" action="/rh/historique" style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <?php $statut = (string)($filters['statut'] ?? ''); ?>
        <a href="/rh/historique" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Traitées (par défaut)</a>
        <a href="/rh/historique?statut=approuvee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Approuvées</a>
        <a href="/rh/historique?statut=refusee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Refusées</a>

        <select name="departement_id" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto" onchange="this.form.submit()">
          <option value="0">Tous les départements</option>
          <?php foreach (($departements ?? []) as $d): ?>
            <option value="<?= (int)($d['id'] ?? 0) ?>" <?= ((int)($filters['departement_id'] ?? 0) === (int)($d['id'] ?? 0)) ? 'selected' : '' ?>><?= esc((string)($d['nom'] ?? '')) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="statut" value="<?= esc($statut) ?>"/>
      </form>

      <div class="data-card">
        <div class="data-card-head"><h3>Demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Type</th><th>Période</th><th>Durée</th><th>Statut</th></tr>
          </thead>
          <tbody>
          <?php if (empty($demandes)): ?>
            <tr><td colspan="6" class="td-muted" style="padding:1rem">Aucune demande.</td></tr>
          <?php else: ?>
            <?php foreach ($demandes as $demande): ?>
              <tr>
                <td><?= esc(($demande['prenom'] ?? '').' '.($demande['nom'] ?? '')) ?></td>
                <td class="td-muted"><?= esc((string)($demande['departement_nom'] ?? '')) ?></td>
                <td><?= esc((string)($demande['type_libelle'] ?? '')) ?></td>
                <td class="td-mono"><?= esc((string)($demande['date_debut'] ?? '')) ?> → <?= esc((string)($demande['date_fin'] ?? '')) ?></td>
                <td class="td-mono"><?= (int)($demande['nb_jours'] ?? 0) ?> j</td>
                <?php $s=(string)($demande['statut'] ?? ''); ?>
                <td><span class="statut <?= $s==='approuvee'?'s-approuvee':($s==='refusee'?'s-refusee':'s-attente') ?>"><?= esc($s) ?></span></td>
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
