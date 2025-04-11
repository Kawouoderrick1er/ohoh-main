<?php // c:\xampp\htdocs\ohoh-main\admin_dashboard.php (MODIFIED Sidebar Link)
session_start(); // Très important pour la sécurité !

// --- Vérification de Sécurité Essentielle ---
// !! DÉCOMMENTEZ CECI EN PRODUCTION !!
/*
if (!isset($_SESSION['admin_id'])) {
    // Rediriger vers la page de connexion si non connecté
    header("Location: admin_login.php?error=auth_required");
    exit();
}
*/

$admin_name = $_SESSION['admin_name'] ?? 'Admin'; // Utiliser le nom de l'admin stocké en session
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur - D-X-T</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- VOTRE CSS PERSONNALISÉ -->
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>

<body>
    <div class="sidebar">
        <div class="admin-info">
             <i class="fas fa-user-shield"></i>
            <h2><?php echo htmlspecialchars($admin_name); ?></h2>
        </div>
        <div class="menu">
            <!-- Lien Tableau de Bord (Aperçu) -->
            <a href="#" data-url="admin_dashboard_overview.php" onclick="loadContent(this, event)" class="active">
                <i class="fas fa-tachometer-alt fa-fw"></i>Tableau de Bord
            </a>
            <hr class="text-secondary opacity-50 my-2">

            <!-- Liens de Gestion Principaux -->
            <a href="#" data-url="gestion_generique.php?table=apprenants" onclick="loadContent(this, event)">
                <i class="fas fa-users fa-fw"></i>Apprenants
            </a>
             <a href="#" data-url="gestion_generique.php?table=formateurs" onclick="loadContent(this, event)">
                <i class="fas fa-chalkboard-teacher fa-fw"></i>Formateurs
            </a>
            <a href="#" data-url="gestion_generique.php?table=formations" onclick="loadContent(this, event)"> <!-- MODIFIED table=formations -->
                <i class="fas fa-graduation-cap fa-fw"></i>Formations <!-- MODIFIED icon and text -->
            </a>
            <!-- <a href="#" data-url="gestion_generique.php?table=lecons" onclick="loadContent(this, event)"> -->
                <!-- <i class="fas fa-tasks fa-fw"></i>Leçons --> <!-- REMOVED -->
            <!-- </a> -->
             <a href="#" data-url="gestion_generique.php?table=inscriptions" onclick="loadContent(this, event)">
                <i class="fas fa-user-check fa-fw"></i>Inscriptions/Accès <!-- MODIFIED text -->
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Autres Outils -->
             <a href="#" data-url="gestion_generique.php?table=messages_contact" onclick="loadContent(this, event)">
                <i class="fas fa-envelope-open-text fa-fw"></i>Messages Contact
            </a>
            <a href="#" data-url="gestion_emails.php" onclick="loadContent(this, event)">
                <i class="fas fa-paper-plane fa-fw"></i>Envoyer Email
            </a>

             <hr class="text-secondary opacity-50 my-2">

             <!-- Administration Système -->
            <a href="#" data-url="gestion_generique.php?table=administrateurs" onclick="loadContent(this, event)">
                <i class="fas fa-user-cog fa-fw"></i>Administrateurs
            </a>
        </div>
         <div class="logout-link">
            <a href="admin_logout.php">
                <i class="fas fa-sign-out-alt"></i>Déconnexion
            </a>
        </div>
    </div>

    <div class="content">
        <div id="content-area" class="content-area">
            <!-- Contenu chargé dynamiquement ici -->
            <div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Chargement initial...</div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/admin_dashboard.js" defer></script>
    <script>
        // Dans admin_dashboard.js (ou un fichier JS chargé après)

// Fonction pour soumettre les formulaires via AJAX
function handleFormSubmit(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêche la soumission standard

            const formData = new FormData(form);
            const url = form.action; // URL définie dans l'attribut action du formulaire
            const contentArea = document.getElementById('content-area');
            const submitButton = form.querySelector('button[type="submit"]');

            // Désactiver bouton + indicateur chargement
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sauvegarde...';
            }
            if(formId === 'addForm') {
                // Optionally hide the add form while processing
                // document.getElementById('addFormContainer').style.display = 'none';
            } else if (formId === 'editForm') {
                 // Optionally hide modal footer or show spinner in modal
            }


            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Attend le fragment HTML en réponse
            })
            .then(html => {
                contentArea.innerHTML = html; // Met à jour la zone de contenu
                // Réinitialiser les composants Bootstrap si nécessaire (ex: tooltips, modals)
                reinitializeBootstrapComponents();
                // Si c'était le modal d'édition, le fermer
                if (formId === 'editForm') {
                    const editModalEl = document.getElementById('editModal');
                    const modalInstance = bootstrap.Modal.getInstance(editModalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
                 // Réactiver le bouton (peut être fait dans reinitialize ou ici)
                 // Note: Le bouton n'existera plus si le formulaire a disparu après succès
                 // Il vaut mieux gérer l'état dans la réponse HTML ou après le rechargement

                 // Faire défiler vers le haut pour voir le message
                 contentArea.scrollIntoView({ behavior: 'smooth' });

            })
            .catch(error => {
                console.error(`Erreur lors de la soumission du formulaire ${formId}:`, error);
                // Afficher une erreur générique à l'utilisateur si nécessaire
                // Peut-être réactiver le bouton ici en cas d'erreur réseau
                 if (submitButton) {
                    submitButton.disabled = false;
                    // Remettre le texte original du bouton
                    if(formId === 'addForm') submitButton.innerHTML = '<i class="fas fa-check me-1"></i> Ajouter';
                    else if (formId === 'editForm') submitButton.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
                 }
                 // Afficher un message d'erreur persistant ?
                 const errorDiv = document.createElement('div');
                 errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                 errorDiv.innerHTML = `Erreur lors de la soumission. Vérifiez la console. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                 contentArea.insertBefore(errorDiv, contentArea.firstChild);
            });
        });
    }
}

// Appeler cette fonction pour les deux formulaires (peut-être dans loadContent après chargement)
// handleFormSubmit('addForm');
// handleFormSubmit('editForm');

// Assurez-vous que ces fonctions existent et fonctionnent
function toggleAddForm() {
    const container = document.getElementById('addFormContainer');
    if (container) {
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
}

function showEditModal(data, pk_name) {
     console.log("Editing data:", data); // Debug
     const modal = new bootstrap.Modal(document.getElementById('editModal'));
     const form = document.getElementById('editForm');
     if (!form || !modal) return;

     // Reset form first
     form.reset();

     // Populate form fields
     form.elements[pk_name].value = data[pk_name] || ''; // Set the hidden ID

     for (const key in data) {
         if (form.elements[key] && key !== pk_name) {
             const element = form.elements[key];
             if (element.type === 'checkbox') {
                 element.checked = !!data[key];
             } else {
                 element.value = data[key] === null ? '' : data[key]; // Handle null values
             }
         } else if (form.elements[`edit_${key}`]) { // Check for elements with edit_ prefix if needed
             form.elements[`edit_${key}`].value = data[key] === null ? '' : data[key];
         }
     }
     modal.show();
}

function deleteItem(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet élément ?")) {
        const form = document.getElementById('deleteForm');
        if (form) {
            form.elements['delete_id'].value = id; // Set the ID in the hidden delete form
            // Submit the delete form via AJAX (similar to add/edit) or standard submit
            // Using standard submit for simplicity here, but AJAX is better UX
            // form.submit();

            // AJAX Submission for Delete:
            const formData = new FormData(form);
            const url = form.action;
            const contentArea = document.getElementById('content-area');

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                contentArea.innerHTML = html;
                reinitializeBootstrapComponents();
                contentArea.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                 console.error('Erreur lors de la suppression:', error);
                 const errorDiv = document.createElement('div');
                 errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                 errorDiv.innerHTML = `Erreur lors de la suppression. Vérifiez la console. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                 contentArea.insertBefore(errorDiv, contentArea.firstChild);
            });
        }
    }
}

// Fonction pour réinitialiser les composants JS après chargement AJAX
function reinitializeBootstrapComponents() {
    // Réactiver les tooltips Bootstrap s'ils sont utilisés
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    // Ajouter d'autres réinitialisations si nécessaire (popovers, etc.)

    // IMPORTANT: Rattacher les gestionnaires d'événements si le contenu rechargé les a perdus
    // Si handleFormSubmit est appelé globalement une fois, c'est bon.
    // Sinon, il faut le rappeler ici ou utiliser la délégation d'événements.
    // handleFormSubmit('addForm'); // Probablement pas nécessaire si appelé une fois au début
    // handleFormSubmit('editForm'); // Probablement pas nécessaire si appelé une fois au début
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
     // Attacher les gestionnaires aux formulaires qui pourraient exister au chargement initial
     // (Bien que dans ce cas, ils sont chargés via AJAX)
     // Il est peut-être préférable d'appeler handleFormSubmit dans la fonction loadContent
     // après que le contenu ait été injecté.

     // Exemple d'appel initial si nécessaire
     // handleFormSubmit('addForm');
     // handleFormSubmit('editForm');
});


// Modifier la fonction loadContent pour attacher les gestionnaires après chargement
function loadContent(element, event) {
    event.preventDefault();
    const url = element.getAttribute('data-url');
    const contentArea = document.getElementById('content-area');
    const menuLinks = document.querySelectorAll('.sidebar .menu a');

    // Afficher indicateur chargement
    contentArea.innerHTML = '<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';

    // Gérer classe active
    menuLinks.forEach(link => link.classList.remove('active'));
    element.classList.add('active');

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            contentArea.innerHTML = data;
            reinitializeBootstrapComponents(); // Réinitialiser BS
            // *** ATTACHER LES GESTIONNAIRES ICI ***
            handleFormSubmit('addForm');
            handleFormSubmit('editForm');
            // *** FIN ATTACHEMENT ***
        })
        .catch(error => {
            console.error('Erreur chargement contenu:', error);
            contentArea.innerHTML = `<div class="alert alert-danger" role="alert">Erreur lors du chargement du contenu: ${error.message}</div>`;
        });
}


// Charger le contenu initial (Overview)
document.addEventListener('DOMContentLoaded', () => {
    const initialLink = document.querySelector('.sidebar .menu a.active');
    if (initialLink) {
        // Simuler un clic pour charger le contenu initial via AJAX
         loadContent(initialLink, new Event('click')); // Passer un Event simulé
    }
     reinitializeBootstrapComponents(); // Initialiser BS pour la page de base
});


    </script>

</body>
</html>
