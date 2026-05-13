<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Validation RH</title>
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
      <li><a href="/rh" class="active"><i class="bi bi-inbox"></i> Demandes à traiter
        <?php if (!empty($counts['en_attente'])): ?>
          <span class="nav-badge alert"><?= (int)$counts['en_attente'] ?></span>
        <?php endif; ?>
      </a></li>
      <li><a href="/rh/historique"><i class="bi bi-archive"></i> Historique</a></li>
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
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb">Accueil <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <?php if (!empty($counts['en_attente'])): ?>
        <span style="font-size:.8rem;background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= (int)$counts['en_attente'] ?> en attente
        </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="content">

      <?php $flashError = session()->getFlashdata('error'); ?>
      <?php if ($flashError): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(is_string($flashError) ? $flashError : 'Une erreur est survenue.') ?>
        </div>
      <?php endif; ?>

      <?php $flashSuccess = session()->getFlashdata('success'); ?>
      <?php if ($flashSuccess): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(is_string($flashSuccess) ? $flashSuccess : 'Succès.') ?>
        </div>
      <?php endif; ?>

      <form method="get" action="/rh" style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <?php $statut = (string)($filters['statut'] ?? 'en_attente'); ?>
        <a href="/rh?statut=" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Tous (<?= (int)($counts['all'] ?? 0) ?>)</a>
        <a href="/rh?statut=en_attente" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">En attente (<?= (int)($counts['en_attente'] ?? 0) ?>)</a>
        <a href="/rh?statut=approuvee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Approuvées (<?= (int)($counts['approuvee'] ?? 0) ?>)</a>
        <a href="/rh?statut=refusee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none">Refusées (<?= (int)($counts['refusee'] ?? 0) ?>)</a>

        <select name="departement_id" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto" onchange="this.form.submit()">
          <option value="0">Tous les départements</option>
          <?php foreach (($departements ?? []) as $d): ?>
            <?php $deptName = is_string($d['nom'] ?? null) ? $d['nom'] : ''; ?>
            <option value="<?= (int)($d['id'] ?? 0) ?>" <?= ((int)($filters['departement_id'] ?? 0) === (int)($d['id'] ?? 0)) ? 'selected' : '' ?>><?= esc((string) $deptName) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="statut" value="<?= esc($statut) ?>"/>
      </form>

      <div class="data-card">
        <div class="data-card-head"><h3>Demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php if (empty($demandes)): ?>
            <tr><td colspan="7" class="td-muted" style="padding:1rem">Aucune demande.</td></tr>
          <?php else: ?>
            <?php foreach ($demandes as $demande): ?>
              <?php
                $id = (int)($demande['id'] ?? 0);
                $solde = $soldesRestants[$id] ?? null;
                $stat = (string)($demande['statut'] ?? '');
              ?>
              <tr>
                <td>
                  <div class="profile-row">
                    <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">EMP</div>
                    <div class="profile-info">
                      <div class="pname"><?= esc(($demande['prenom'] ?? '').' '.($demande['nom'] ?? '')) ?></div>
                      <?php $deptNom = is_string($demande['departement_nom'] ?? null) ? $demande['departement_nom'] : ''; ?>
                      <div class="pdept"><?= esc((string) $deptNom) ?></div>
                    </div>
                  </div>
                </td>
                <?php $typeLib = is_string($demande['type_libelle'] ?? null) ? $demande['type_libelle'] : ''; ?>
                <td><span class="type-badge"><?= esc((string) $typeLib) ?></span></td>
                <?php $dd = is_string($demande['date_debut'] ?? null) ? $demande['date_debut'] : ''; ?>
                <?php $df = is_string($demande['date_fin'] ?? null) ? $demande['date_fin'] : ''; ?>
                <td class="td-muted" style="font-size:.8rem"><?= esc((string) $dd) ?> – <?= esc((string) $df) ?></td>
                <td class="td-mono"><?= (int)($demande['nb_jours'] ?? 0) ?> j</td>
                <td>
                  <?php if ($solde === null): ?>
                    <span class="td-muted" style="font-family:'DM Mono',monospace;font-size:.82rem">—</span>
                  <?php else: ?>
                    <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= ($solde < (int)($demande['nb_jours'] ?? 0)) ? 'var(--danger)' : 'var(--success)' ?>;font-weight:500">
                      <?= (int)$solde ?> j
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($stat === 'en_attente'): ?>
                    <span class="statut s-attente">en attente</span>
                  <?php elseif ($stat === 'approuvee'): ?>
                    <span class="statut s-approuvee">approuvée</span>
                  <?php else: ?>
                    <span class="statut s-refusee">refusée</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($stat === 'en_attente'): ?>
                    <div class="action-btns" style="gap:6px;flex-wrap:wrap">
                      <form method="post" action="/rh/demandes/<?= $id ?>/approve" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="commentaire_rh" value=""/>
                        <button class="btn-sm btn-approve" type="submit"><i class="bi bi-check-lg"></i> Approuver</button>
                      </form>
                      <form method="post" action="/rh/demandes/<?= $id ?>/refuse" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="text" name="commentaire_rh" class="f-input" placeholder="Commentaire (optionnel)" style="height:32px;padding:6px 10px;font-size:.8rem;width:200px"/>
                        <button class="btn-sm btn-refuse" type="submit"><i class="bi bi-x-lg"></i> Refuser</button>
                      </form>
                    </div>
                  <?php else: ?>
                    <span class="td-muted" style="font-size:.75rem">Traité</span>
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
