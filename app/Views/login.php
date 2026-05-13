<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Connexion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/assets/css/main.css"/>
<link rel="stylesheet" href="/assets/css/login.css"/>
</head>
<body>
<section class="auth-page geo-bg">
<div class="auth-split">

  <div class="auth-left">
    <div>
      <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
      <p class="auth-left-text" style="margin-top:2rem">
        <strong>Bienvenue sur votre espace RH.</strong>
        Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
      </p>
    </div>
    <div class="auth-roles">
      <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:4px">Comptes de démonstration</div>
      <div class="role-pill">
        <i class="bi bi-shield-check"></i>
        <div><div class="role-pill-name">Administrateur</div><div class="role-pill-cred">admin@techmada.mg · admin123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person-check"></i>
        <div><div class="role-pill-name">Responsable RH</div><div class="role-pill-cred">rh@techmada.mg · rh123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person"></i>
        <div><div class="role-pill-name">Employé</div><div class="role-pill-cred">employe@techmada.mg · emp123</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <p class="auth-title">Connexion</p>
    <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

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

    <?php $errors = session()->getFlashdata('errors'); $errors = is_array($errors) ? $errors : []; ?>

    <form method="post" action="/login">
      <?= csrf_field() ?>
      <div class="f-group">
        <label class="f-label">Adresse email</label>
        <input name="email" type="email" class="f-input" placeholder="vous@techmada.mg" value="<?= esc(old('email') ?? '') ?>"/>
        <?php if (!empty($errors['email'])): ?><div class="text-danger small mt-1"><?= esc(is_string($errors['email']) ? $errors['email'] : '') ?></div><?php endif; ?>
      </div>
      <div class="f-group">
        <label class="f-label">Mot de passe</label>
        <input name="password" type="password" class="f-input" placeholder="••••••••" value=""/>
        <?php if (!empty($errors['password'])): ?><div class="text-danger small mt-1"><?= esc(is_string($errors['password']) ? $errors['password'] : '') ?></div><?php endif; ?>
      </div>
      <button type="submit" class="btn-primary" style="margin-top:.5rem">
        Se connecter <i class="bi bi-arrow-right-short"></i>
      </button>
    </form>
  </div>

</div>
</section>
</body>
</html>
