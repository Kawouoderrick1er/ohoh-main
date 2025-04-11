<?php // c:\xampp\htdocs\ohoh\navbar.php (Page d'accueil)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'navigation.php'; // Inclut <!DOCTYPE>, <head>, <body>, <nav>, <main>
?>

<head>
    <title>D-X-T - Formation Professionnelle Numérique</title>
    <!-- Les liens CSS sont dans navigation.php -->
    <!-- Pas de <style> ici, tout est dans style.css -->
</head>

<!-- Section Héros -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in">
                <h1 class="display-4">Développez vos compétences. <br>Atteignez vos objectifs !</h1>
                <p class="lead">Accédez à un réseau global de formations pour booster votre carrière et réaliser vos ambitions stratégiques.</p>
                <a href="formation.php" class="btn btn-primary btn-lg">Explorer les formations</a>
                <a href="contact.php" class="btn btn-outline-light btn-lg">Nous contacter</a>
            </div>
            <div class="col-lg-6 text-center fade-in" style="transition-delay: 0.2s;">
                <!-- !! Remplacer par une image pertinente !! -->
                <img src="Images/png/etu.png" alt="Apprentissage en ligne" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</section>

<!-- Section Pourquoi nous choisir / Fonctionnalités clés -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Pourquoi choisir D-X-T ?</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 d-flex">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h3>Formateurs Experts</h3>
                    <p>Apprenez auprès de professionnels reconnus dans leur domaine.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex">
                <div class="feature-card animate-on-scroll" style="transition-delay: 0.1s;">
                    <div class="feature-icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>Contenu Pratique</h3>
                    <p>Des formations axées sur les compétences réelles demandées par le marché.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex">
                <div class="feature-card animate-on-scroll" style="transition-delay: 0.2s;">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h3>Flexibilité Totale</h3>
                    <p>Apprenez à votre rythme, où et quand vous voulez, grâce à notre plateforme en ligne.</p>
                </div>
            </div>
             <div class="col-md-6 col-lg-3 d-flex">
                <div class="feature-card animate-on-scroll" style="transition-delay: 0.3s;">
                    <div class="feature-icon"><i class="fas fa-certificate"></i></div>
                    <h3>Certifications</h3>
                    <p>Validez vos compétences avec des certifications reconnues.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section À Propos (simplifiée) -->
<section class="section-padding bg-light"> <!-- Fond légèrement différent -->
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-on-scroll">
                 <!-- !! Remplacer par une image pertinente !! -->
                <img src="Images/IMG-20250319-WA0029.jpg" alt="À propos de D-X-T" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6 animate-on-scroll" style="transition-delay: 0.1s;">
                <h2 class="section-title text-start mb-4">Bienvenue chez D-X-T</h2>
                <p class="lead mb-4">Spécialistes des technologies numériques, nous vous aidons à maîtriser les outils et compétences de demain.</p>
                <p>Nos formations en ligne de haute qualité sont conçues pour vous propulser vers vos objectifs professionnels. Rejoignez une communauté dynamique d'apprenants et d'experts.</p>
                <a href="A_propos.php" class="btn btn-outline-primary">En savoir plus sur nous</a>
            </div>
        </div>
    </div>
</section>

<!-- Section Services/Formations populaires (Aperçu) -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Nos Formations Populaires</h2>
        <div class="row g-4">
            <!-- Exemple de 3 formations - Remplacer par des données réelles si possible -->
            <div class="col-md-4 d-flex">
                 <div class="card formation-card animate-on-scroll">
                    <img src="Images/formation-placeholder.jpg" class="card-img-top" alt="Formation Web">
                    <div class="card-body">
                        <h5 class="card-title">Développement Web Complet</h5>
                        <p class="card-text">Maîtrisez HTML, CSS, JavaScript, PHP et plus encore pour créer des sites web modernes.</p>
                    </div>
                     <div class="card-footer">
                         <small>Débutant à Intermédiaire</small>
                         <a href="details_formation.php?id=1" class="btn btn-primary btn-sm float-end">Détails</a> <!-- Mettre un ID réel -->
                     </div>
                </div>
            </div>
             <div class="col-md-4 d-flex">
                 <div class="card formation-card animate-on-scroll" style="transition-delay: 0.1s;">
                    <img src="Images/marketing-placeholder.jpg" class="card-img-top" alt="Formation Marketing">
                    <div class="card-body">
                        <h5 class="card-title">Marketing Digital Stratégique</h5>
                        <p class="card-text">Apprenez les stratégies SEO, SEM, réseaux sociaux et content marketing.</p>
                    </div>
                     <div class="card-footer">
                         <small>Tous niveaux</small>
                         <a href="details_formation.php?id=2" class="btn btn-primary btn-sm float-end">Détails</a> <!-- Mettre un ID réel -->
                     </div>
                </div>
            </div>
             <div class="col-md-4 d-flex">
                 <div class="card formation-card animate-on-scroll" style="transition-delay: 0.2s;">
                    <img src="Images/design-placeholder.jpg" class="card-img-top" alt="Formation Design">
                    <div class="card-body">
                        <h5 class="card-title">Design UI/UX Professionnel</h5>
                        <p class="card-text">Créez des interfaces utilisateur intuitives et esthétiques avec Figma et Adobe XD.</p>
                    </div>
                     <div class="card-footer">
                         <small>Intermédiaire à Avancé</small>
                         <a href="details_formation.php?id=3" class="btn btn-primary btn-sm float-end">Détails</a> <!-- Mettre un ID réel -->
                     </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 animate-on-scroll">
            <a href="formation.php" class="btn btn-primary btn-lg">Voir toutes les formations</a>
        </div>
    </div>
</section>

<!-- Section Contact (simplifiée) -->
<section class="section-padding bg-light">
     <div class="container text-center">
         <h2 class="section-title animate-on-scroll">Prêt à commencer ?</h2>
         <p class="lead mb-4 animate-on-scroll" style="transition-delay: 0.1s;">Contactez-nous pour discuter de vos besoins ou inscrivez-vous dès aujourd'hui.</p>
         <a href="contact.php" class="btn btn-success btn-lg me-2 animate-on-scroll" style="transition-delay: 0.2s;">Nous Contacter</a>
         <a href="inscription.php" class="btn btn-outline-primary btn-lg animate-on-scroll" style="transition-delay: 0.3s;">S'inscrire</a>
     </div>
</section>


<?php include 'foote.php'; // Inclut la fermeture de </main>, <footer>, <scripts>, </body>, </html> ?>
