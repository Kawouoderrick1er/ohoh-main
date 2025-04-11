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
                // Vérifier si la valeur existe réellement dans les options
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
 * Demande confirmation et soumet le formulaire de suppression.
 * @param {number|string} id - L'ID de l'élément à supprimer.
 */
function deleteItem(id) {
    const deleteForm = contentArea.querySelector('#deleteForm');
    const deleteIdInput = contentArea.querySelector('#delete_id');

    if (!deleteForm || !deleteIdInput) {
         console.error("deleteItem: #deleteForm ou #delete_id non trouvé.");
         alert("Erreur : Impossible d'initier la suppression.");
         return;
    }

    if (confirm("Êtes-vous sûr de vouloir supprimer cet enregistrement ?\nCette action est irréversible.")) {
        deleteIdInput.value = id;
        // Soumettre le formulaire via JS. Le rechargement se fera par la réponse du serveur.
        deleteForm.submit();
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
         // Fallback pour activer le lien du tableau de bord si aucun élément n'est fourni
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
    contentArea.innerHTML = `<div class="loading-indicator"><i class="fas fa-spinner"></i> Chargement en cours...</div>`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}. Réponse: ${text.substring(0, 500)}...`); });
            }
            return response.text();
        })
        .then(data => {
            contentArea.innerHTML = data;
            reinitializeBootstrapComponents(); // Réinitialiser après injection
            window.scrollTo(0, 0);
        })
        .catch(error => {
            contentArea.innerHTML = `<div class="alert alert-danger" role="alert"><strong>Erreur chargement:</strong> ${error.message}. URL: <code>${url}</code>. Voir console.</div>`;
            console.error('Erreur Fetch:', error);
        });
}

/**
 * Réinitialise les composants JS (Bootstrap, Chart.js) après un chargement AJAX.
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

    // Chart.js (si présent dans le contenu chargé et si Chart est défini)
    const chartCanvas = contentArea.querySelector('#myAreaChart');
    if (chartCanvas && typeof Chart !== 'undefined') {
        const existingChart = Chart.getChart(chartCanvas);
        if (existingChart) existingChart.destroy();
        try {
            // Recréer le graphique (la configuration doit être adaptée ou récupérée)
            new Chart(chartCanvas, {
                 type: 'line',
                 data: {
                     labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin"], // Exemple
                     datasets: [{
                         label: "Activité",
                         data: [10, 20, 15, 25, 22, 30], // Exemple
                         borderColor: 'rgb(75, 192, 192)',
                         tension: 0.1
                     }]
                 },
                 options: { responsive: true, maintainAspectRatio: false }
             });
            console.log("Chart.js initialisé.");
        } catch (error) { console.error("Erreur init Chart.js:", error); }
    } else if (chartCanvas) {
         console.warn("Canvas #myAreaChart trouvé, mais Chart.js n'est pas chargé ou défini.");
    }

    // Attacher l'écouteur pour le formulaire d'email (s'il existe)
     const emailForm = contentArea.querySelector('#emailForm');
     if (emailForm && emailForm.getAttribute('data-ajax-submit') !== 'true') { // Eviter double attachement
         emailForm.setAttribute('data-ajax-submit', 'true');
         emailForm.addEventListener('submit', handleEmailFormSubmit);
     }
}

/**
 * Gère la soumission AJAX du formulaire d'email.
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
    // Vérifier si contentArea existe avant de continuer
    if (!contentArea) {
        console.error("Erreur critique: L'élément #content-area n'a pas été trouvé dans le DOM.");
        return;
    }

    const overviewUrl = 'admin_dashboard_overview.php';
    const overviewLinkElement = document.querySelector(`.sidebar .menu a[data-url="${overviewUrl}"]`);
    if (overviewLinkElement) {
        loadContent(overviewLinkElement, null);
    } else {
        console.warn(`Lien menu pour '${overviewUrl}' non trouvé. Tentative de chargement direct.`);
        // Créer un élément temporaire pour passer à loadContent
        const tempElement = document.createElement('a');
        tempElement.setAttribute('data-url', overviewUrl);
        loadContent(tempElement, null);
    }
});
