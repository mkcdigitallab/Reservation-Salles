<?php

declare(strict_types=1);

$title = 'Salles disponibles';

require __DIR__ . '/../layout/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <span class="hero-badge">RÉSERVATION UNIVERSITAIRE</span>

        <h1>
            Trouvez la salle idéale<br>
            pour votre activité.
        </h1>

        <p>
            Consultez les salles disponibles et réservez
            rapidement l’espace adapté à vos besoins.
        </p>

        <a href="/reservations/create" class="btn btn-primary">
            Réserver une salle
        </a>
    </div>

    <div class="hero-decoration">
        <div class="hero-card">
            <span class="hero-card-icon">🏫</span>

            <div>
                <strong><?= count($salles ?? []) ?></strong>
                <span>salles disponibles</span>
            </div>
        </div>
    </div>
</section>


<section class="section">

    <div class="section-heading">
        <div>
            <span class="section-label">NOS ESPACES</span>

            <h2>Salles disponibles</h2>

            <p>
                Choisissez l’espace qui correspond à votre activité.
            </p>
        </div>
    </div>


    <?php if (empty($salles)): ?>

        <div class="empty-state">
            <div class="empty-icon">🏫</div>

            <h3>Aucune salle disponible</h3>

            <p>
                Aucune salle active n'est actuellement disponible.
            </p>
        </div>

    <?php else: ?>

        <div class="room-grid">

            <?php foreach ($salles as $salle): ?>

                <article class="room-card">

                    <div class="room-card-top">

                        <div class="room-icon">
                            🏫
                        </div>

                        <span class="status-badge">
                            Disponible
                        </span>

                    </div>


                    <div class="room-card-body">

                        <span class="room-type">
                            <?= htmlspecialchars(
                                ucfirst($salle->type),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars(
                                $salle->nom,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>

                        <p class="room-building">
                            📍
                            <?= htmlspecialchars(
                                $salle->batiment,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>


                        <div class="room-info">

                            <div>
                                <span class="info-label">
                                    Capacité
                                </span>

                                <strong>
                                    <?= $salle->capacite ?>
                                    personnes
                                </strong>
                            </div>

                        </div>


                        <a
                            href="/reservations/create?salle=<?= $salle->id ?>"
                            class="room-button"
                        >
                            Réserver cette salle
                            <span>→</span>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
