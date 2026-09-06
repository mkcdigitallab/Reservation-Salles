<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'Réservation de salles' ?></title>

    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>

<header class="site-header">
    <div class="container navbar">

        <a href="/" class="logo">
            <span class="logo-icon">R</span>
            <span>Room<span>Book</span></span>
        </a>

        <nav class="navigation">
            <a href="/" class="nav-link active">Salles</a>
            <a href="/reservations/create" class="nav-link">
                Réserver
            </a>
        </nav>

        <a href="/reservations/create" class="btn btn-primary">
            + Nouvelle réservation
        </a>

    </div>
</header>

<main class="main-content">
    <div class="container">
