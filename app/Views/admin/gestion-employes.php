<?php
// Vue dynamique de gestion des employés
// Données fournies par AdminController::employes()
$employes = $employes ?? [];
$departements = $departements ?? [];
$filters = $filters ?? ['departement_id' => 0, 'q' => ''];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>TechMada RH — Gestion employés</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/main.css" />
  <link rel="stylesheet" href="/assets/css/gestion-employes.css" />
</head>

<body>

  <div class="app-wrap">

    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
      </div>
      <ul class="sidebar-nav" style="margin-top:1rem">
        <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
        <li><a href="<?= base_url('admin/employes') ?>" class="active"><i class="bi bi-people"></i> Employés</a></li>
        <li><a href="<?= base_url('admin/departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
        <li><a href="<?= base_url('admin/types-conge') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
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
          <div class="topbar-title">Gestion des employés</div>
          <div class="topbar-breadcrumb"><a href="<?= base_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
        </div>
        <div class="topbar-actions">
          <a href="#ajout-employe" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
        </div>
      </div>

      <div class="content">

        <div class="form-section" id="ajout-employe">
          <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
          <form action="<?= base_url('admin/employes') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Prénom</label>
                <input type="text" class="f-input" name="prenom" placeholder="Jean" required />
              </div>
              <div class="f-group">
                <label class="f-label">Nom</label>
                <input type="text" class="f-input" name="nom" placeholder="Rakoto" required />
              </div>
              <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" class="f-input" name="email" placeholder="jean.rakoto@techmada.mg" required />
              </div>
              <div class="f-group">
                <label class="f-label">Mot de passe initial</label>
                <input type="password" class="f-input" name="password" placeholder="À communiquer à l'employé" required />
              </div>
              <div class="f-group">
                <label class="f-label">Département</label>
                <select class="f-select" name="departement_id" required>
                  <option value="">Sélectionner...</option>
                  <?php foreach ($departements as $dept): ?>
                    <option value="<?= (int)($dept['id'] ?? 0) ?>">
                      <?= esc($dept['nom'] ?? '') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="f-group">
                <label class="f-label">Rôle</label>
                <select class="f-select" name="role" required>
                  <option value="employe">Employé</option>
                  <option value="rh">Responsable RH</option>
                  <option value="admin">Administrateur</option>
                </select>
              </div>
              <div class="f-group">
                <label class="f-label">Date d'embauche</label>
                <input type="date" class="f-input" name="date_embauche" value="<?= date('Y-m-d') ?>" />
              </div>
            </div>
            <div class="flash flash-info" style="margin-bottom:1rem">
              <i class="bi bi-info-circle-fill"></i>
              <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
              <button type="reset" class="btn-secondary">Réinitialiser</button>
            </div>
          </form>
        </div>

        <div class="data-card">
          <div class="data-card-head">
            <h3>Tous les employés</h3>
            <div style="display:flex;gap:6px">
              <form action="<?= base_url('admin/employes') ?>" method="get" style="display:flex;gap:6px">
                <input type="text" class="f-input" name="q" placeholder="Rechercher..." value="<?= esc($filters['q'] ?? '') ?>" style="width:200px;padding:6px 10px;font-size:.8rem" />
                <select class="f-select" name="departement_id" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
                  <option value="0">Tous les depts</option>
                  <?php foreach ($departements as $dept): ?>
                    <option value="<?= (int)($dept['id'] ?? 0) ?>" <?= (($filters['departement_id'] ?? 0) == ($dept['id'] ?? 0) ? 'selected' : '') ?>>
                      <?= esc($dept['nom'] ?? '') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (!empty($filters['q']) || (($filters['departement_id'] ?? 0) > 0)): ?>
                  <a href="<?= base_url('admin/employes') ?>" class="btn-secondary" style="padding:6px 12px;font-size:.8rem">Effacer</a>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <table class="tbl">
            <thead>
              <tr>
                <th>Employé</th>
                <th>Département</th>
                <th>Rôle</th>
                <th>Embauche</th>
                <th>Statut</th>
                <th>Solde annuel</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($employes)): ?>
                <?php foreach ($employes as $emp): ?>
                  <tr>
                    <td>
                      <div class="profile-row">
                        <div class="avatar" style="width:32px;height:32px;font-size:.68rem;background:<?= getInitialsColor($emp['nom'] ?? '', $emp['prenom'] ?? '') ?>">
                          <?= getInitials($emp['nom'] ?? '', $emp['prenom'] ?? '') ?>
                        </div>
                        <div class="profile-info">
                          <div class="pname"><?= esc(($emp['prenom'] ?? '') . ' ' . ($emp['nom'] ?? '')) ?></div>
                          <div class="pdept"><?= esc($emp['email'] ?? '') ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="td-muted"><?= esc($emp['departement_nom'] ?? 'Non assigné') ?></td>
                    <td>
                      <span class="type-badge" style="background:<?= getRoleColor($emp['role'] ?? '') ?>;color:<?= getRoleTextColor($emp['role'] ?? '') ?>">
                        <?= esc($emp['role'] ?? '') ?>
                      </span>
                    </td>
                    <td class="td-muted td-mono" style="font-size:.78rem"><?= $emp['date_embauche'] ? formatDate($emp['date_embauche']) : '—' ?></td>
                    <td>
                      <span class="statut <?= $emp['actif'] ? 's-approuvee' : 's-annulee' ?>" style="font-size:.68rem">
                        <?= $emp['actif'] ? 'actif' : 'inactif' ?>
                      </span>
                    </td>
                    <td>
                      <?php
                      // Calcul du solde annuel (à implémenter complètement)
                      $soldeModel = new \App\Models\SoldeModel();
                      $soldeActuel = $soldeModel->getSoldeActuel((int)($emp['id'] ?? 0), (int)date('Y'));
                      $totalAttribue = $soldeActuel['total_attribue'] ?? 0;
                      $totalPris = $soldeActuel['total_pris'] ?? 0;
                      $restant = $totalAttribue - $totalPris;
                      ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= $restant > 5 ? 'var(--forest)' : 'var(--amber)' ?>">
                        <?= number_format($restant, 1) ?> / <?= number_format($totalAttribue, 1) ?> j
                      </span>
                    </td>
                    <td>
                      <div class="action-btns">
                        <?php if ($emp['actif']): ?>
                          <a class="btn-sm btn-edit" href="<?= base_url('admin/employes/' . (int)($emp['id'] ?? 0) . '/edit') ?>"><i class="bi bi-pencil"></i> Éditer</a>
                          <button type="button" class="btn-sm btn-del" onclick="toggleEmploye(<?= (int)($emp['id'] ?? 0) ?>)"><i class="bi bi-slash-circle"></i></button>
                        <?php else: ?>
                          <button type="button" class="btn-sm btn-view" onclick="toggleEmploye(<?= (int)($emp['id'] ?? 0) ?>)"><i class="bi bi-arrow-counterclockwise"></i> Réactiver</button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" style="text-align:center;padding:1rem;color:var(--muted)">Aucun employé trouvé</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
      <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
    </div>

  </div>

  <script>
    function toggleEmploye(id) {
      if (confirm('Êtes-vous sûr de vouloir modifier le statut de cet employé ?')) {
        window.location.href = '<?= base_url('admin/employes') ?>/' + id + '/toggle';
      }
    }
  </script>
</body>

</html>