const zoekveld = document.querySelector('#game-zoeken');
const kaarten = document.querySelectorAll('[data-game-kaart]');

zoekveld?.addEventListener('input', () => {
    const zoekterm = zoekveld.value.trim().toLowerCase();

    kaarten.forEach((kaart) => {
        const titel = kaart.querySelector('h2, h3').textContent.toLowerCase();
        kaart.classList.toggle('hidden', !titel.includes(zoekterm));
    });
});

const menuKnop = document.querySelector('#menu-knop');
const hoofdmenu = document.querySelector('#hoofdmenu');

if (menuKnop && hoofdmenu) {
    menuKnop.addEventListener('click', () => {
        const isOpen = menuKnop.getAttribute('aria-expanded') === 'true';

        menuKnop.setAttribute('aria-expanded', String(!isOpen));
        hoofdmenu.classList.toggle('hidden', isOpen);
        hoofdmenu.classList.toggle('flex', !isOpen);
    });
}