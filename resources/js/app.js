document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-mobile-nav-toggle]');
    const nav = document.querySelector('[data-mobile-nav]');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('hidden');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('submit', (event) => {
            if (!window.confirm(element.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
});
