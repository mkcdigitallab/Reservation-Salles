<?php

declare(strict_types=1);

$title = 'Réservation invalide';

require __DIR__ . '/../layout/header.php';
?>

<section class="form-page">
    <div class="form-header">
        <span class="section-label">ERREUR DE VALIDATION</span>
        <h1>Réservation impossible</h1>
        <p><?= htmlspecialchars($message ?? 'Les données envoyées sont invalides.', ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="form-actions">
        <a href="/reservations/create" class="btn btn-primary">Retour au formulaire</a>
        <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
