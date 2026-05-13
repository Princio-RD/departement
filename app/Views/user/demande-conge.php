<?php
/** @var array<int, array<string, mixed>> $typesConge */
/** @var array<int, array<string, mixed>> $soldes */
/** @var array<int, array<string, int>> $soldesByType */

$typesConge   = $typesConge ?? [];
$soldes       = $soldes ?? [];
$soldesByType = $soldesByType ?? [];

$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');
$flashSuccess = is_string($flashSuccess) ? $flashSuccess : '';
$flashError   = is_string($flashError) ? $flashError : '';

$errType = session('errors.type_conge_id');
$errDeb  = session('errors.date_debut');
$errFin  = session('errors.date_fin');
$errType = is_string($errType) ? $errType : '';
$errDeb  = is_string($errDeb) ? $errDeb : '';
$errFin  = is_string($errFin) ? $errFin : '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Nouvelle demande</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/css/main.css"/>
<link rel="stylesheet" href="/css/demande-conge.css"/>
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
      <li><a href="/user/conges/nouveau" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/user/conges"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
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
        <div class="topbar-title">Nouvelle demande de congé</div>
        <div class="topbar-breadcrumb">
          <a href="/user/dashboard">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
    </div>

    <div class="content">

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

        <div>
          <div class="form-section">
            <h3>Détails de la demande</h3>

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

            <form method="post" action="/user/conges" novalidate>
              <?= csrf_field() ?>

              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
                <select class="f-select" name="type_conge_id" required>
                  <option value="">-- Choisir un type --</option>
                  <?php foreach ($typesConge as $t): ?>
                    <?php
                      $typeId = isset($t['id']) ? (int) $t['id'] : 0;
                      $libelle = isset($t['libelle']) && is_string($t['libelle']) ? $t['libelle'] : '';
                      $restantTxt = '';
                      if ($typeId && isset($soldesByType[$typeId])) {
                          $restantTxt = ' (' . (int) $soldesByType[$typeId]['restant'] . ' j restants)';
                      }
                    ?>
                    <option value="<?= $typeId ?>" <?= set_select('type_conge_id', (string) $typeId) ?>>
                      <?= esc($libelle) ?><?= esc($restantTxt) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if ($errType !== ''): ?>
                  <div class="f-error" style="display:block"><i class="bi bi-exclamation-circle"></i> <?= esc($errType) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-grid-2" style="margin-bottom:1rem">
                <div class="f-group">
                  <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_debut" value="<?= esc(set_value('date_debut')) ?>" required/>
                  <?php if ($errDeb !== ''): ?>
                    <div class="f-error" style="display:block"><i class="bi bi-exclamation-circle"></i> <?= esc($errDeb) ?></div>
                  <?php endif; ?>
                </div>
                <div class="f-group">
                  <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_fin" value="<?= esc(set_value('date_fin')) ?>" required/>
                  <?php if ($errFin !== ''): ?>
                    <div class="f-error" style="display:block"><i class="bi bi-exclamation-circle"></i> <?= esc($errFin) ?></div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Motif (optionnel)</label>
                <textarea class="f-textarea" name="motif" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc(set_value('motif')) ?></textarea>
                <div class="f-hint">Le motif est visible par le responsable RH.</div>
              </div>

              <div class="form-actions">
                <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
                <a href="/user/dashboard" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
              </div>
            </form>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (empty($soldes)): ?>
                <div style="font-size:.8rem;color:var(--muted)">Aucun solde disponible.</div>
              <?php else: ?>
                <?php foreach ($soldes as $s): ?>
                  <?php
                    $restant = max(0, (int) ($s['jours_attribues'] ?? 0) - (int) ($s['jours_pris'] ?? 0));
                    $soldeLib = isset($s['libelle']) && is_string($s['libelle']) ? $s['libelle'] : '';
                    $attribuesForPct = max(1, (int) ($s['jours_attribues'] ?? 0));
                    $pct = (int) min(100, round(($restant / $attribuesForPct) * 100));
                  ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:.8rem;color:var(--ink)"><?= esc($soldeLib) ?></span>
                      <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= $restant ?> j</span>
                    </div>
                    <div class="solde-bar"><div class="solde-fill" style="width:<?= $pct ?>%"></div></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Préavis minimum : 48h avant la date de début</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
  </div>

</div>

</body>
</html>
