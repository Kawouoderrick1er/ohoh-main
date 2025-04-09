<?php
// Vous pouvez démarrer une session ici si nécessaire pour vérifier la connexion utilisateur,
// mais pour une page "À Propos" publique, ce n'est généralement pas requis.
// session_start();

// Inclure la barre de navigation commune
include 'navigation.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos de D-X-T</title>
    <!-- Bootstrap CSS (déjà inclus via navigation.php si c'est le cas, sinon décommentez) -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Font Awesome (déjà inclus via navigation.php si c'est le cas, sinon décommentez) -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> -->

    <!-- <style>
        /* Styles spécifiques pour la page À Propos */
        body {
            background-color: #f8f9fa; /* Fond légèrement gris */
        }

        .about-header {
            background: linear-gradient(to right, rgba(106, 17, 203, 0.8), rgba(37, 117, 252, 0.8)), url('Images/background-placeholder.jpg') no-repeat center center; /* Image de fond avec dégradé semi-transparent */
            background-size: cover;
            color: white;
            padding: 4rem 1rem;
            text-align: center;
            margin-bottom: 3rem;
            border-radius: 0 0 15px 15px; /* Coins arrondis en bas */
        }

        .about-header h1 {
            font-size: 2.8rem;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        .about-section {
            padding: 2.5rem 0;
        }

        .about-section h2 {
            color: #343a40;
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-align: center;
            position: relative;
            padding-bottom: 0.5rem;
        }
        /* Ligne décorative sous les titres h2 */
        .about-section h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background-color: #007bff;
            margin: 0.5rem auto 0;
        }


        .about-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .team-section {
            background-color: #ffffff; /* Fond blanc pour la section équipe */
             border-radius: 8px;
             padding: 2rem 1rem;
             box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .team-member img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%; /* Images rondes pour l'équipe */
            margin-bottom: 1rem;
            border: 3px solid #007bff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .team-member h5 {
            margin-bottom: 0.25rem;
            color: #343a40;
        }

        .team-member p {
            color: #6c757d; /* Gris pour le rôle */
            font-style: italic;
        }

        .values-section .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Assurer la même hauteur pour les cartes */
        }

        .values-section .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .values-section .card-icon {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 1rem;
        }

        .values-section .card-title {
            color: #343a40;
            font-weight: bold;
        }

    </style> -->
</head>
<body>

    <!-- Section En-tête de la page À Propos -->
    <header class="about-header">
        <div class="container">
            <h1>À Propos de D-X-T</h1>
            <p class="lead">Découvrez notre histoire, notre mission et l'équipe derrière notre succès.</p>
        </div>
    </header>

    <!-- Contenu Principal -->
    <main class="container">

        <!-- Section: Notre Histoire / Mission -->
        <section class="about-section" id="mission">
            <div class="row align-items-center about-content">
                <div class="col-md-6 order-md-2">
                    <!-- !! IMPORTANT: Remplacez 'Images/about-mission.jpg' par une image pertinente !! -->
                    <img src="Images/about-mission.jpg" alt="Notre Mission D-X-T" class="img-fluid">
                </div>
                <div class="col-md-6 order-md-1">
                    <h2>Notre Mission</h2>
                    <p>Chez D-X-T, notre mission est de **révolutionner l'accès à la formation professionnelle** dans les domaines technologiques. Nous nous engageons à fournir des parcours d'apprentissage de haute qualité, pratiques et adaptés aux besoins du marché du travail actuel.</p>
                    <p>Nous croyons fermement que le développement des compétences numériques est essentiel pour l'épanouissement individuel et la croissance économique. C'est pourquoi nous mettons tout en œuvre pour rendre nos formations accessibles, engageantes et efficaces.</p>
                    <!-- Vous pouvez ajouter un bouton ici si pertinent -->
                    <!-- <a href="formation.php" class="btn btn-primary mt-3">Découvrir nos formations</a> -->
                </div>
            </div>
        </section>

        <!-- Section: Nos Valeurs -->
        <section class="about-section values-section" id="valeurs">
            <h2>Nos Valeurs Fondamentales</h2>
            <div class="row g-4">
                <!-- Valeur 1: Expertise -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                            <h5 class="card-title">Expertise</h5>
                            <p class="card-text">Nous nous appuyons sur des formateurs experts et des contenus constamment mis à jour pour garantir l'excellence de nos formations.</p>
                        </div>
                    </div>
                </div>
                <!-- Valeur 2: Innovation -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="card-icon"><i class="fas fa-lightbulb"></i></div>
                            <h5 class="card-title">Innovation</h5>
                            <p class="card-text">Nous intégrons les dernières technologies et méthodologies pédagogiques pour une expérience d'apprentissage stimulante.</p>
                        </div>
                    </div>
                </div>
                <!-- Valeur 3: Accessibilité -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="card-icon"><i class="fas fa-universal-access"></i></div>
                            <h5 class="card-title">Accessibilité</h5>
                            <p class="card-text">Nous œuvrons pour rendre la formation de qualité accessible à tous, où qu'ils soient et quel que soit leur parcours.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section: Notre Équipe -->
        <section class="about-section team-section" id="equipe">
            <h2>Notre Équipe Dévouée</h2>
            <p class="text-center text-muted mb-4">Rencontrez quelques membres clés qui rendent D-X-T possible.</p>
            <div class="row text-center">
                <!-- Membre 1 -->
                <div class="col-md-4 team-member mb-4">
                    <!-- !! IMPORTANT: Remplacez 'Images/team-member1.jpg' et les infos !! -->
                    <img src="Images/team-member1.jpg" alt="Membre de l'équipe 1">
                    <h5>Nom Prénom 1</h5>
                    <p>Fondateur & CEO</p>
                </div>
                <!-- Membre 2 -->
                <div class="col-md-4 team-member mb-4">
                     <!-- !! IMPORTANT: Remplacez 'Images/team-member2.jpg' et les infos !! -->
                    <img src="Images/team-member2.jpg" alt="Membre de l'équipe 2">
                    <h5>Nom Prénom 2</h5>
                    <p>Responsable Pédagogique</p>
                </div>
                <!-- Membre 3 -->
                <div class="col-md-4 team-member mb-4">
                     <!-- !! IMPORTANT: Remplacez 'Images/team-member3.jpg' et les infos !! -->
                    <img src="Images/team-member3.jpg" alt="Membre de l'équipe 3">
                    <h5>Nom Prénom 3</h5>
                    <p>Développeur Principal</p>
                </div>
                <!-- Ajoutez d'autres membres si nécessaire -->
            </div>
        </section>

         <!-- Section: Pourquoi nous choisir (Optionnel) -->
        <section class="about-section" id="pourquoi-nous">
            <div class="row align-items-center about-content">
                 <div class="col-md-6">
                    <!-- !! IMPORTANT: Remplacez 'Images/why-choose-us.jpg' par une image pertinente !! -->
                    <img src="Images/why-choose-us.jpg" alt="Pourquoi choisir D-X-T" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>Pourquoi Choisir D-X-T ?</h2>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Formations axées sur la pratique et les compétences recherchées.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Flexibilité d'apprentissage avec accès en ligne 24/7.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Communauté d'apprenants et de professionnels pour le partage.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Accompagnement personnalisé par nos experts.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Certifications reconnues pour booster votre carrière.</li>
                    </ul>
                     <a href="contact.php" class="btn btn-outline-primary mt-3">Contactez-nous pour en savoir plus</a>
                </div>
            </div>
        </section>

    </main>

    <?php
    // Inclure le pied de page commun
    include 'foote.php';
    ?>

    <!-- Bootstrap JS Bundle (déjà inclus via foote.php ou navigation.php si c'est le cas, sinon décommentez) -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
