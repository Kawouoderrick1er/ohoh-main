<?php // c:\xampp\htdocs\ohoh-main\blog.php
session_start();
require_once 'base.php'; // Pour une future connexion DB si nécessaire

// --- DONNÉES FACTICES POUR LE BLOG ---
// Remplacez ceci par une requête à votre table 'blog_posts' plus tard
$blog_posts = [
    [
        'id' => 1,
        'title' => 'Les Tendances du Développement Web en 2025',
        'excerpt' => 'Découvrez les technologies et méthodologies qui façonneront le développement web l\'année prochaine. De l\'IA aux frameworks JavaScript...',
        'image' => 'Images/blog/web-trends.jpg', // Chemin vers votre image
        'author' => 'Alice Dubois',
        'date' => '2024-05-15',
        'category' => 'Développement Web'
    ],
    [
        'id' => 2,
        'title' => 'Maîtriser le SEO : Guide pour Débutants',
        'excerpt' => 'Le référencement naturel est crucial. Apprenez les bases pour améliorer la visibilité de votre site sur Google et autres moteurs de recherche.',
        'image' => 'Images/blog/seo-guide.jpg', // Chemin vers votre image
        'author' => 'Bob Martin',
        'date' => '2024-05-10',
        'category' => 'Marketing Digital'
    ],
    [
        'id' => 3,
        'title' => 'Pourquoi Choisir une Formation en Cybersécurité ?',
        'excerpt' => 'Le domaine de la cybersécurité est en pleine expansion. Découvrez les opportunités de carrière et l\'importance de se former.',
        'image' => 'Images/blog/cybersecurity.jpg', // Chemin vers votre image
        'author' => 'Charlie Dupont',
        'date' => '2024-05-05',
        'category' => 'Carrière Tech'
    ],
    [
        'id' => 4,
        'title' => 'UI vs UX Design : Comprendre la Différence',
        'excerpt' => 'Bien que souvent utilisés ensemble, l\'UI et l\'UX design sont distincts. Clarifions leurs rôles et leur importance dans la création de produits numériques.',
        'image' => 'Images/blog/ui-ux.jpg', // Chemin vers votre image
        'author' => 'Diana Moreau',
        'date' => '2024-04-28',
        'category' => 'Design'
    ],
     [
        'id' => 5,
        'title' => 'Introduction à PHP 8 et ses Nouveautés',
        'excerpt' => 'PHP continue d\'évoluer. Explorez les fonctionnalités clés introduites dans PHP 8 qui améliorent la performance et la syntaxe.',
        'image' => 'Images/blog/php8.jpg', // Chemin vers votre image
        'author' => 'Alice Dubois',
        'date' => '2024-04-20',
        'category' => 'Développement Web'
    ],
     [
        'id' => 6,
        'title' => '5 Outils Indispensables pour le Marketing sur les Réseaux Sociaux',
        'excerpt' => 'Optimisez votre stratégie social media avec ces outils essentiels pour la planification, l\'analyse et l\'engagement.',
        'image' => 'Images/blog/social-media-tools.jpg', // Chemin vers votre image
        'author' => 'Bob Martin',
        'date' => '2024-04-12',
        'category' => 'Marketing Digital'
    ],
    // Ajoutez plus d'articles factices ici si vous voulez
];
// --- FIN DES DONNÉES FACTICES ---


// Inclure la navigation (qui inclut DOCTYPE, head, nav, ouvre <main>)
include 'navigation.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - D-X-T</title>
    <!-- Styles spécifiques pour la page Blog -->
    <style>
        .blog-header {
            background: linear-gradient(to right, rgba(0, 123, 255, 0.7), rgba(0, 86, 179, 0.7)), url('Images/blog/blog-banner.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 3rem 1rem;
            text-align: center;
            margin-bottom: 3rem;
            border-radius: 0 0 10px 10px;
        }
        .blog-header h1 {
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .blog-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Assure que les cartes dans une rangée ont la même hauteur */
            display: flex;
            flex-direction: column;
            border: none; /* Enlever la bordure par défaut */
            border-radius: 10px; /* Coins plus arrondis */
            overflow: hidden; /* Pour que l'image respecte les coins arrondis */
        }
        .blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .blog-card .card-img-top {
            width: 100%;
            height: 200px; /* Hauteur fixe pour les images */
            object-fit: cover; /* Assure que l'image remplit l'espace sans déformation */
        }
        .blog-card .card-body {
            flex-grow: 1; /* Permet au corps de la carte de s'étendre */
            display: flex;
            flex-direction: column; /* Organise le contenu verticalement */
            padding: 1.25rem;
        }
        .blog-card .card-title {
            font-weight: 600; /* Un peu plus gras */
            color: #343a40;
            margin-bottom: 0.75rem;
        }
        .blog-card .blog-meta {
            font-size: 0.8rem;
            color: #6c757d; /* Gris pour les métadonnées */
            margin-bottom: 1rem;
        }
        .blog-card .blog-meta span {
            margin-right: 10px; /* Espace entre les métadonnées */
        }
        .blog-card .blog-meta i {
            color: #007bff; /* Icônes en bleu */
        }
        .blog-card .card-text {
             font-size: 0.95rem;
             color: #555;
             flex-grow: 1; /* Permet au texte de prendre l'espace restant */
             margin-bottom: 1.25rem; /* Espace avant le bouton */
        }
        .blog-card .btn {
            margin-top: auto; /* Pousse le bouton vers le bas */
            align-self: flex-start; /* Aligne le bouton à gauche */
            font-size: 0.9rem;
        }

        /* Styles pour les animations (si animations.js est configuré) */
        /* Assurez-vous que ces styles sont compatibles avec votre assets/js/animations.js */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

    </style>
</head>

<!-- Le <main> est déjà ouvert dans navigation.php -->

    <!-- En-tête de la section Blog -->
    <header class="blog-header animate-on-scroll">
        <div class="container">
            <h1>Notre Blog</h1>
            <p class="lead">Actualités, conseils et tendances du monde numérique.</p>
        </div>
    </header>

    <!-- Contenu principal du Blog -->
    <div class="container mb-5">

        <?php if (empty($blog_posts)): ?>
            <div class="alert alert-info text-center animate-on-scroll" role="alert">
                <i class="fas fa-info-circle me-2"></i> Aucun article de blog n'est disponible pour le moment. Revenez bientôt !
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($blog_posts as $index => $post): ?>
                    <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                        <div class="card blog-card shadow-sm animate-on-scroll" style="transition-delay: <?php echo $index * 0.1; ?>s;">
                            <img src="<?php echo htmlspecialchars(!empty($post['image']) ? $post['image'] : 'Images/blog/default-placeholder.jpg'); ?>"
                                 class="card-img-top"
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <div class="blog-meta text-muted small mb-2">
                                    <?php if (!empty($post['author'])): ?>
                                        <span><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($post['author']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($post['date'])): ?>
                                        <span><i class="fas fa-calendar-alt me-1"></i><?php echo date("d M Y", strtotime($post['date'])); ?></span>
                                    <?php endif; ?>
                                     <?php if (!empty($post['category'])): ?>
                                        <span><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($post['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                <!-- Lien vers l'article complet (page à créer : blog_post.php) -->
                                <a href="blog_post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                                    Lire la suite <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Optionnel: Pagination (si vous avez beaucoup d'articles) -->
            <!--
            <nav aria-label="Page navigation blog" class="mt-5 animate-on-scroll" style="transition-delay: <?php echo count($blog_posts) * 0.1; ?>s;">
              <ul class="pagination justify-content-center">
                <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
              </ul>
            </nav>
            -->

        <?php endif; ?>
    </div>

<!-- Le </main> sera fermé dans foote.php -->

<?php
// Inclure le pied de page (qui inclut la fermeture de </main>, footer, scripts JS, fermeture <body> et <html>)
include 'foote.php';
?>
