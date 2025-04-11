// c:\xampp\htdocs\ohoh\assets\js\animations.js

document.addEventListener('DOMContentLoaded', () => {

    // --- Animation Fade-in et Slide-in au défilement ---
    const animatedElements = document.querySelectorAll('.fade-in, .animate-on-scroll, .form-group-animate');

    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries, observerInstance) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Ajoute la classe 'is-visible' pour déclencher l'animation CSS
                    entry.target.classList.add('is-visible');
                    // Optionnel: arrêter d'observer une fois l'animation jouée
                    observerInstance.unobserve(entry.target);
                }
                // Pas besoin de 'else' si on n'anime qu'une fois
            });
        }, {
            threshold: 0.1 // Déclencher quand 10% de l'élément est visible
        });

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }

    // --- Optionnel: Effet Parallax simple sur le Hero ---
    const hero = document.querySelector('.hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrollPosition = window.pageYOffset;
            // Applique un léger déplacement vertical inverse au fond du hero
            hero.style.backgroundPositionY = `${scrollPosition * 0.4}px`;
        });
    }

});
