function abrirModalReserva() {
    document.getElementById("modalOverlayReserva").style.display = "flex";
}

function fecharModal() {
    document.getElementById("modalOverlayReserva").style.display = "none";
}

document.addEventListener('DOMContentLoaded', function() {
    const menuButtonUser = document.querySelector('.menu-button-user');
    const menuDropdownUser = document.querySelector('.menu-dropdown-user');

    menuButtonUser.addEventListener('click', function() {
        menuDropdownUser.classList.toggle('show');
        menuButtonUser.setAttribute('aria-expanded', menuDropdownUser.classList.contains('show'));
    });
    
    document.addEventListener('click', function(event) {
        if (!menuButtonUser.contains(event.target) && !menuDropdownUser.contains(event.target)) {
            menuDropdownUser.classList.remove('show');
            menuButtonUser.setAttribute('aria-expanded', 'false');
        }
    });
});
