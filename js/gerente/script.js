function paginaMesa() {
    document.getElementById("pagina-mesa").style.display = "flex";
    document.getElementById("pagina-cardapio").style.display = "none";
    document.getElementById("pagina-funcionarios").style.display = "none";
}

function paginaCardapio() {
    document.getElementById("pagina-mesa").style.display = "none";
    document.getElementById("pagina-cardapio").style.display = "block";
    document.getElementById("pagina-funcionarios").style.display = "none";
}

function paginaFuncionario() {
    document.getElementById("pagina-mesa").style.display = "none";
    document.getElementById("pagina-cardapio").style.display = "none";
    document.getElementById("pagina-funcionarios").style.display = "block";
}

function fecharModal() {
    document.getElementById("modalOverlayMesa").style.display = "none";
    document.getElementById("modalOverlayCardapio").style.display = "none";
    document.getElementById("modalOverlayFuncionario").style.display = "none";
}

document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.querySelector('.menu-button');
    const menuDropdown = document.querySelector('.menu-dropdown');
    const menuButtonUser = document.querySelector('.menu-button-user');
    const menuDropdownUser = document.querySelector('.menu-dropdown-user');

    menuButton.addEventListener('click', function() {
        menuDropdown.classList.toggle('show');
        menuButton.setAttribute('aria-expanded', menuDropdown.classList.contains('show'));
    });

    // Fechar o menu quando clicar fora dele
    document.addEventListener('click', function(event) {
        if (!menuButton.contains(event.target) && !menuDropdown.contains(event.target)) {
            menuDropdown.classList.remove('show');
            menuButton.setAttribute('aria-expanded', 'false');
        }
    });

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