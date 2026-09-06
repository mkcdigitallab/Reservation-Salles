<?php

declare(strict_types=1);

$title = 'Réserver une salle';

require __DIR__ . '/../layout/header.php';
?>

<section class="form-page">

    <div class="form-header">
        <span class="section-label">NOUVELLE RÉSERVATION</span>

        <h1>Réserver une salle</h1>

        <p>
            Complétez les informations ci-dessous pour réserver
            votre espace.
        </p>
    </div>


    <div class="reservation-form-card">

        <form method="POST" action="/reservations">

            <div class="form-section">

                <h2>Informations personnelles</h2>

                <div class="form-grid">

                    <div class="form-group">

                        <label for="responsable">
                            Responsable
                        </label>

                        <input
                            type="text"
                            id="responsable"
                            name="responsable"
                            placeholder="Votre nom complet"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Adresse email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="exemple@email.com"
                            required
                        >

                    </div>

                </div>

            </div>


            <div class="form-section">

                <h2>Informations de réservation</h2>

                <div class="form-group">

                    <label for="salle_id">
                        Salle
                    </label>

                    <select
                        id="salle_id"
                        name="salle_id"
                        required
                    >

                        <option value="">
                            Sélectionnez une salle
                        </option>

                        <?php foreach ($salles ?? [] as $salle): ?>

                            <option
                                value="<?= $salle->id ?>"
                                <?= (
                                    isset($salleSelectionnee)
                                    && $salleSelectionnee->id === $salle->id
                                )
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= htmlspecialchars(
                                    $salle->nom,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                                —
                                <?= $salle->capacite ?> personnes
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label for="motif">
                        Motif de la réservation
                    </label>

                    <textarea
                        id="motif"
                        name="motif"
                        rows="4"
                        placeholder="Exemple : cours, réunion, soutenance..."
                        required
                    ></textarea>

                </div>


                <div class="form-grid">

                    <div class="form-group">

                        <label for="date_debut">
                            Date et heure de début
                        </label>

                        <input
                            type="datetime-local"
                            id="date_debut"
                            name="date_debut"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="date_fin">
                            Date et heure de fin
                        </label>

                        <input
                            type="datetime-local"
                            id="date_fin"
                            name="date_fin"
                            required
                        >

                    </div>

                </div>

            </div>


            <div class="form-actions">

                <a href="/" class="btn btn-secondary">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Confirmer la réservation
                </button>

            </div>

        </form>

    </div>

</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
