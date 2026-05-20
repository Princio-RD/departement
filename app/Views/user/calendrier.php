<?php
$eventsJson = $eventsJson ?? '[]';
$summary = $summary ?? [];
$typeStats = $typeStats ?? [];
$annee = (int) ($annee ?? date('Y'));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>TechMada RH — Vue calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/main.css" />
    <script src="/assets/js/index.global.js"></script>
    <script src="/assets/js/user-calendrier.js" defer></script>
    <style>
        #calendar {
            min-height: 760px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
        }
    </style>
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
                <li><a href="<?= base_url('user/calendrier') ?>" class="active"><i class="bi bi-calendar-week"></i> Vue calendrier</a></li>
                <li><a href="<?= base_url('user/conges/nouveau') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
                <li><a href="<?= base_url('user/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
                <li><a href="<?= base_url('user/profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
            </ul>
        </aside>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="topbar-title">Vue calendrier</div>
                    <div class="topbar-breadcrumb">Congés planifiés — <?= $annee ?></div>
                </div>
                <div class="topbar-actions">
                    <a href="<?= base_url('user/conges/nouveau') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-circle"></i> Nouvelle demande</a>
                </div>
            </div>

            <div class="content">
                <div class="metrics" style="margin-bottom:1.25rem">
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
                        </div>
                        <div class="metric-val"><?= (int)($summary['en_attente'] ?? 0) ?></div>
                        <div class="metric-label">En attente</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div>
                        </div>
                        <div class="metric-val"><?= (int)($summary['approuvee'] ?? 0) ?></div>
                        <div class="metric-label">Approuvées</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div>
                        </div>
                        <div class="metric-val"><?= (int)($summary['refusee'] ?? 0) ?></div>
                        <div class="metric-label">Refusées</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-icon mi-blue"><i class="bi bi-x-octagon"></i></div>
                        </div>
                        <div class="metric-val"><?= (int)($summary['annulee'] ?? 0) ?></div>
                        <div class="metric-label">Annulées</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">
                    <div id="calendar" data-events='<?= esc($eventsJson, 'attr') ?>'></div>
                    <div style="display:flex;flex-direction:column;gap:1rem">
                        <div class="data-card" style="margin:0">
                            <div class="data-card-head">
                                <h3>Types de congé</h3>
                            </div>
                            <div style="padding:.9rem 1rem;display:flex;flex-direction:column;gap:.65rem">
                                <?php if (empty($typeStats)): ?>
                                    <div class="td-muted" style="font-size:.8rem">Aucune donnée.</div>
                                <?php else: ?>
                                    <?php foreach ($typeStats as $label => $count): ?>
                                        <div style="display:flex;justify-content:space-between;gap:12px;font-size:.82rem"><span><?= esc($label) ?></span><strong><?= (int)$count ?></strong></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flash flash-info" style="margin:0">
                            <i class="bi bi-info-circle-fill"></i>
                            <span style="font-size:.8rem">Les événements sont chargés depuis la base et mis à jour selon vos demandes de congé.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>