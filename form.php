<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de Formations</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #f4f4f4;
}

header {
    width: 100%;
    background-color: #4CAF50;
    color: white;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    width: 50px;
    height: 50px;
}

h1 {
    flex-grow: 1;
    text-align: center;
}

.search-bar {
    display: flex;
    align-items: center;
}

#search-bar {
    padding: 0.5rem;
    border: none;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-bar button {
    background-color: white;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
}

aside {
    width: 100%;
    max-width: 300px;
    background-color: white;
    padding: 1rem;
    margin: 1rem 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

aside h2 {
    text-align: center;
}

aside div {
    margin-bottom: 1rem;
}

main {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
    max-width: 1200px;
}

.formation-card {
    background-color: white;
    margin: 1rem;
    padding: 1rem;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.formation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.formation-card img {
    width: 100%;
    height: auto;
    border-radius: 4px;
}

.formation-card h3 {
    margin: 0.5rem 0;
}

.formation-card p {
    margin: 0.5rem 0;
}

.formation-card button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.formation-card button:hover {
    background-color: #45a049;
}

footer {
    width: 100%;
    background-color: #333;
    color: white;
    padding: 1rem;
    text-align: center;
    margin-top: auto;
}

.footer-nav {
    margin-top: 1rem;
}

.footer-nav a {
    color: white;
    margin: 0 0.5rem;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-nav a:hover {
    color: #4CAF50;
}
</style>

<body>
    <header>
        <div class="header-container">
            <img src="logo.png" alt="Logo" class="logo">
            <h1>Catalogue de Formations</h1>
            <div class="search-bar">
                <input type="text" id="search-bar" placeholder="Rechercher une formation...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>
    </header>
    <aside>
        <h2>Filtres</h2>
        <div>
            <label for="filiere">Filière:</label>
            <select id="filiere">
                <option value="informatique">Informatique</option>
                <option value="gestion">Gestion</option>
                <option value="design">Design</option>
            </select>
        </div>
        <div>
            <label for="niveau">Niveau:</label>
            <select id="niveau">
                <option value="debutant">Débutant</option>
                <option value="intermediaire">Intermédiaire</option>
                <option value="avance">Avancé</option>
            </select>
        </div>
        <div>
            <label for="duree">Durée:</label>
            <select id="duree">
                <option value="moins_1_mois">Moins d'un mois</option>
                <option value="1_3_mois">1-3 mois</option>
                <option value="plus_3_mois">Plus de 3 mois</option>
            </select>
        </div>
        <div>
            <label for="langue">Langue:</label>
            <select id="langue">
                <option value="francais">Français</option>
                <option value="anglais">Anglais</option>
                <option value="espagnol">Espagnol</option>
            </select>
        </div>
    </aside>
    <main>
        <div class="formations-container">
            <!-- Repeat the formation-card structure for 20 formations -->
            <div class="formation-card" data-filiere="informatique" data-niveau="debutant" data-duree="2mois" data-langue="francais">
                <img src="formation1.jpg" alt="Formation 1">
                <h3>Titre de la Formation 1</h3>
                <p>Description de la formation 1...</p>
                <p>Durée: 2 mois</p>
                <button>S'inscrire</button>
            </div>
            <div class="formation-card" data-filiere="gestion" data-niveau="intermediaire" data-duree="1mois" data-langue="anglais">
                <img src="formation2.jpg" alt="Formation 2">
                <h3>Titre de la Formation 2</h3>
                <p>Description de la formation 2...</p>
                <p>Durée: 1 mois</p>
                <button>S'inscrire</button>
            </div>
            <div class="formation-card" data-filiere="design" data-niveau="avance" data-duree="3mois" data-langue="espagnol">
                <img src="formation3.jpg" alt="Formation 3">
                <h3>Titre de la Formation 3</h3>
                <p>Description de la formation 3...</p>
                <p>Durée: 3 mois</p>
                <button>S'inscrire</button>
            </div>
            <!-- Add more formation cards as needed -->
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Catalogue de Formations. Tous droits réservés.</p>
        <div class="footer-nav">
            <a href="#">Contact</a>
            <a href="#">À propos</a>
            <a href="#">Mentions légales</a>
        </div>
    </footer>
    <script src="scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const filiereSelect = document.getElementById('filiere');
    const niveauSelect = document.getElementById('niveau');
    const dureeSelect = document.getElementById('duree');
    const langueSelect = document.getElementById('langue');
    const formationCards = document.querySelectorAll('.formation-card');

    function filterFormations() {
        const searchText = searchBar.value.toLowerCase();
        const filiere = filiereSelect.value;
        const niveau = niveauSelect.value;
        const duree = dureeSelect.value;
        const langue = langueSelect.value;

        formationCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            const cardFiliere = card.dataset.filiere;
            const cardNiveau = card.dataset.niveau;
            const cardDuree = card.dataset.duree;
            const cardLangue = card.dataset.langue;

            if (
                (title.includes(searchText) || description.includes(searchText)) &&
                (filiere === "" || cardFiliere === filiere) &&
                (niveau === "" || cardNiveau === niveau) &&
                (duree === "" || cardDuree === duree) &&
                (langue === "" || cardLangue === langue)
            ) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchBar.addEventListener('input', filterFormations);
    filiereSelect.addEventListener('change', filterFormations);
    niveauSelect.addEventListener('change', filterFormations);
    dureeSelect.addEventListener('change', filterFormations);
    langueSelect.addEventListener('change', filterFormations);
});
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php
    require_once 'foote.php';
    ?>
</body>

</html>