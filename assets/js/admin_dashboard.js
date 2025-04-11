// c:\xampp\htdocs\ohoh-main\assets\js\admin_dashboard.js

// --- Variables Globales ---
const contentArea = document.getElementById('content-area');
const sidebarLinks = document.querySelectorAll('.sidebar .menu a[data-url]');
let editModalInstance = null; // Variable globale pour l'instance du modal d'édition

// --- Fonctions de Gestion Générique ---

/**
 * Affiche ou masque le formulaire d'ajout dans la zone de contenu.
 */
function toggleAddForm() {
    const addFormContainer = contentArea.querySelector('#addFormContainer');
    if (addFormContainer) {
        const isHidden = addFormContainer.style.display === 'none' || addFormContainer.style.display === '';
        addFormContainer.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            const firstInput = addFormContainer.querySelector('input:not([type=hidden]), textarea, select');
            if(firstInput) firstInput.focus();
            // Optionnel: Vider le formulaire quand on l'affiche
            // const addForm = addFormContainer.querySelector('#addForm');
            // if (addForm) addForm.reset();
        }
    } else {
         console.error("toggleAddForm: #addFormContainer non trouvé.");
    }
}

/**
 * Prépare et affiche le modal de modification avec les données.
 * @param {object} data - Les données de la ligne à modifier (objet JS).
 * @param {string} pkName - Le nom de la clé primaire (ex: 'id').
 */
function showEditModal(data, pkName) {
    const modalElement = contentArea.querySelector('#editModal');
    if (!modalElement) {
        console.error("showEditModal: #editModal non trouvé.");
        alert("Erreur : Le formulaire de modification n'a pas pu être chargé.");
        return;
    }

    // Gérer l'instance Bootstrap Modal
    if (!editModalInstance || editModalInstance._element !== modalElement) {
         const existingInstance = bootstrap.Modal.getInstance(modalElement);
         editModalInstance = existingInstance ? existingInstance : new bootstrap.Modal(modalElement);
    }

    const editForm = modalElement.querySelector('#editForm');
    if (!editForm) {
         console.error("showEditModal: #editForm non trouvé."); return;
    }

    // Remplir les champs du formulaire modal
    for (const key in data) {
        const inputElement = editForm.querySelector(`#edit_${key}`);
        if (inputElement) {
            if (inputElement.type === 'password') {
                inputElement.value = ''; // Ne pas pré-remplir le mot de passe
            } else if (inputElement.tagName === 'SELECT') {
                inputElement.value = data[key] ?? ''; // Assigner la valeur pour le select
                if (inputElement.value !== String(data[key]) && data[key] !== null) {
                     console.warn(`Valeur "${data[key]}" non trouvée pour select "${key}".`);
                }
            }
             else {
                inputElement.value = data[key] ?? ''; // Pour text, email, number, etc.
            }
        }
    }

    // Remplir la clé primaire cachée
    const pkInput = editForm.querySelector(`input[name="${pkName}"]`);
    if (pkInput) {
        pkInput.value = data[pkName] ?? '';
    } else {
         console.error(`showEditModal: Input caché pour la clé primaire "${pkName}" non trouvé.`);
    }

    // Afficher le modal
    if (editModalInstance) {
        editModalInstance.show();
    } else {
         console.error("showEditModal: editModalInstance n'a pas pu être créée.");
         alert("Erreur: Impossible d'ouvrir le formulaire.");
    }
}

/**
 * Demande confirmation et effectue la suppression via AJAX/Fetch.
 * @param {number|string} id - L'ID de l'élément à supprimer.
 */
function deleteItem(id) {
    const deleteForm = contentArea.querySelector('#deleteForm'); // Trouver le formulaire caché
    if (!deleteForm) {
        console.error("deleteItem: #deleteForm non trouvé.");
        alert("Erreur : Impossible d'initier la suppression (formulaire manquant).");
        return;
    }

    // Extraire la clé primaire et l'URL de l'action du formulaire caché
    const pkNameInput = deleteForm.querySelector('input[id="delete_id"]');
    if (!pkNameInput) {
         console.error("deleteItem: Input #delete_id non trouvé.");
         alert("Erreur : Impossible d'initier la suppression (champ ID manquant).");
         return;
    }
    const pkName = pkNameInput.name; // Récupère le nom de la clé primaire (ex: 'id')
    const url = deleteForm.action; // Récupère l'URL (ex: gestion_generique.php?table=apprenants)

    if (!url) {
        console.error("deleteItem: Attribut 'action' manquant sur #deleteForm.");
        alert("Erreur : Impossible d'initier la suppression (URL manquante).");
        return;
    }

    if (confirm("Êtes-vous sûr de vouloir supprimer cet enregistrement ?\nCette action est irréversible.")) {
        // Préparer les données à envoyer
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append(pkName, id); // Utilise le nom de la PK récupéré
        formData.append('deleteData', '1'); // Garder la logique du bouton caché

        // Afficher un indicateur de chargement (optionnel, peut être sur la ligne du tableau)
        contentArea.innerHTML = `<div class="loading-indicator"><i class="fas fa-spinner"></i> Suppression en cours...</div>`; // Simple remplacement

        // Envoyer la requête Fetch
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                // Gérer les erreurs HTTP
                return response.text().then(text => { throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}. Réponse: ${text.substring(0, 200)}...`); });
            }
            return response.text(); // Récupérer le fragment HTML mis à jour
        })
        .then(data => {
            // Remplacer le contenu avec la réponse du serveur (qui contient le message et la table mise à jour)
            contentArea.innerHTML = data;
            reinitializeBootstrapComponents(); // Réinitialiser les composants
        })
        .catch(error => {
            console.error('Erreur Fetch (deleteItem):', error);
            // Afficher l'erreur et recharger potentiellement le contenu précédent ?
            // Pour la simplicité, on affiche juste l'erreur dans contentArea
            contentArea.innerHTML = `<div class="alert alert-danger" role="alert"><strong>Erreur lors de la suppression :</strong> ${error.message}. Veuillez recharger la section.</div>`;
            // On pourrait aussi essayer de recharger la table via loadContent ici
            // const currentActiveLink = document.querySelector('.sidebar a.active');
            // if (currentActiveLink) loadContent(currentActiveLink, null);
        });
    }
}


// --- Fonctions AJAX et Initialisation ---

/**
 * Met en surbrillance le lien actif dans la sidebar.
 * @param {HTMLElement} element - L'élément <a> cliqué.
 */
function setActiveLink(element) {
    sidebarLinks.forEach(link => link.classList.remove('active'));
    if(element && element.hasAttribute('data-url')) {
        element.classList.add('active');
    } else {
         const overviewMenuLink = document.querySelector('.sidebar .menu a[data-url="admin_dashboard_overview.php"]');
         if (overviewMenuLink) overviewMenuLink.classList.add('active');
    }
}

/**
 * Charge le contenu dynamiquement via Fetch.
 * @param {HTMLElement} element - L'élément <a> cliqué.
 * @param {Event|null} event - L'événement de clic (ou null si appelé directement).
 */
function loadContent(element, event) {
    if(event) event.preventDefault();
    const url = element.getAttribute('data-url');
    if (!url) { console.error("data-url manquant."); return; }

    setActiveLink(element);
    // Afficher l'indicateur de chargement immédiatement
    contentArea.innerHTML = `<div class="loading-indicator"><i class="fas fa-spinner"></i> Chargement en cours...</div>`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}. Réponse: ${text.substring(0, 500)}...`); });
            }
            return response.text();
        })
        .then(data => {
            // Injecter le nouveau contenu
            contentArea.innerHTML = data;

            // --- AJOUT POUR ANIMATION ---
            // Trouver le conteneur principal du contenu chargé
            const mainContentDiv = contentArea.querySelector('.gestion-section');
            if (mainContentDiv) {
                // Forcer un reflow (peut être nécessaire pour que la transition fonctionne)
                // void mainContentDiv.offsetWidth; // Décommenter si l'animation ne se déclenche pas

                // Ajouter la classe 'is-visible' après un très court délai
                // pour permettre au navigateur de "voir" l'état initial (opacity: 0)
                setTimeout(() => {
                    mainContentDiv.classList.add('is-visible');
                }, 10); // 10 millisecondes suffisent généralement
            }
            // --- FIN AJOUT ANIMATION ---

            reinitializeBootstrapComponents(); // Réinitialiser après injection ET ajout de classe
            window.scrollTo(0, 0);
        })
        .catch(error => {
            contentArea.innerHTML = `<div class="alert alert-danger" role="alert"><strong>Erreur chargement:</strong> ${error.message}. URL: <code>${url}</code>. Voir console.</div>`;
            console.error('Erreur Fetch (loadContent):', error);
        });
}

/**
 * Réinitialise les composants JS (Bootstrap, Chart.js) et attache les gestionnaires AJAX aux formulaires.
 */
function reinitializeBootstrapComponents() {
    // Réinitialiser la variable globale du modal d'édition
    editModalInstance = null;

    // Tooltips
    const tooltipTriggerList = [].slice.call(contentArea.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => {
         const existing = bootstrap.Tooltip.getInstance(el);
         if (existing) existing.dispose();
         new bootstrap.Tooltip(el);
    });

    // Popovers
    const popoverTriggerList = [].slice.call(contentArea.querySelectorAll('[data-bs-toggle="popover"]'));
     popoverTriggerList.forEach(el => {
         const existing = bootstrap.Popover.getInstance(el);
         if (existing) existing.dispose();
         new bootstrap.Popover(el);
    });

    // Chart.js (si présent)
    const chartCanvas = contentArea.querySelector('#myAreaChart');
    if (chartCanvas && typeof Chart !== 'undefined') {
        const existingChart = Chart.getChart(chartCanvas);
        if (existingChart) existingChart.destroy();
        try {
            new Chart(chartCanvas, { /* ... config ... */ });
            console.log("Chart.js initialisé.");
        } catch (error) { console.error("Erreur init Chart.js:", error); }
    }

    // --- Attacher les gestionnaires AJAX aux formulaires ---

    // Formulaire d'ajout générique
    const addForm = contentArea.querySelector('#addForm');
    if (addForm && addForm.getAttribute('data-ajax-submit') !== 'true') {
        addForm.setAttribute('data-ajax-submit', 'true');
        addForm.addEventListener('submit', handleGenericFormSubmit);
    }

    // Formulaire d'édition générique (dans le modal)
    const editForm = contentArea.querySelector('#editForm');
    if (editForm && editForm.getAttribute('data-ajax-submit') !== 'true') {
        editForm.setAttribute('data-ajax-submit', 'true');
        editForm.addEventListener('submit', handleGenericFormSubmit);
    }

    // Formulaire d'email (spécifique)
     const emailForm = contentArea.querySelector('#emailForm');
     if (emailForm && emailForm.getAttribute('data-ajax-submit') !== 'true') {
         emailForm.setAttribute('data-ajax-submit', 'true');
         emailForm.addEventListener('submit', handleEmailFormSubmit); // Utilise sa propre fonction
     }
}

/**
 * Gère la soumission AJAX des formulaires génériques (Ajout/Modification).
 * @param {Event} e - L'événement de soumission.
 */
function handleGenericFormSubmit(e) {
    e.preventDefault(); // Empêche la soumission HTML standard
    const form = e.target;
    const url = form.action;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonHtml = submitButton ? submitButton.innerHTML : ''; // Sauvegarder le texte/icône du bouton

    // Désactiver le bouton et afficher un indicateur
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enregistrement...';
    }

    // Fermer le modal si c'est le formulaire d'édition
    if (form.id === 'editForm' && editModalInstance) {
        editModalInstance.hide();
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}. Réponse: ${text.substring(0, 500)}...`); });
        }
        return response.text(); // Récupérer le fragment HTML mis à jour
    })
    .then(data => {
        // Remplacer le contenu avec la réponse du serveur
        contentArea.innerHTML = data;
        reinitializeBootstrapComponents(); // Réinitialiser les composants et réattacher les listeners
        // Optionnel: Afficher un message de succès plus visible (ex: toast)
        // showToast('Opération réussie !');
    })
    .catch(error => {
        console.error('Erreur Fetch (handleGenericFormSubmit):', error);
        // Afficher une alerte ou un message d'erreur plus persistant
        alert(`Erreur lors de l'enregistrement : ${error.message}`);
        // Réactiver le bouton
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        }
        // Optionnel: Réouvrir le modal si l'erreur vient du formulaire d'édition
        // if (form.id === 'editForm' && editModalInstance) {
        //     editModalInstance.show();
        // }
        // On pourrait aussi recharger la section pour afficher l'erreur renvoyée par PHP
        // const currentActiveLink = document.querySelector('.sidebar a.active');
        // if (currentActiveLink) loadContent(currentActiveLink, null);
    });
}


/**
 * Gère la soumission AJAX du formulaire d'email (fonction existante).
 * @param {Event} e - L'événement de soumission.
 */
function handleEmailFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const url = form.action;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonHtml = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Envoi...';

    fetch(url, { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            // Remplacer le contenu de la section email avec la réponse
            const emailSectionContainer = form.closest('.gestion-section'); // Cherche un conteneur parent
            if (emailSectionContainer) {
                emailSectionContainer.innerHTML = data;
            } else {
                contentArea.innerHTML = data; // Fallback: remplace tout contentArea
            }
            reinitializeBootstrapComponents(); // Réinitialiser après MAJ
        })
        .catch(error => {
            console.error('Erreur soumission email:', error);
            alert('Erreur lors de l\'envoi du formulaire email.');
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        });
}


// --- Chargement Initial ---
document.addEventListener('DOMContentLoaded', () => {
    if (!contentArea) {
        console.error("Erreur critique: #content-area non trouvé.");
        return;
    }
    const overviewUrl = 'admin_dashboard_overview.php';
    const overviewLinkElement = document.querySelector(`.sidebar .menu a[data-url="${overviewUrl}"]`);
    if (overviewLinkElement) {
        loadContent(overviewLinkElement, null);
    } else {
        console.warn(`Lien menu pour '${overviewUrl}' non trouvé.`);
        const tempElement = document.createElement('a');
        tempElement.setAttribute('data-url', overviewUrl);
        loadContent(tempElement, null);
    }
});
