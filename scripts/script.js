// Script para controlar o menu hamburguer
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger-icon');
    const menu = document.getElementById('menu');
    const menuLinks = document.querySelectorAll('.button-container a'); // Seleciona todos os links do menu

    // Função para mostrar/esconder o menu ao clicar no ícone de hambúrguer
    hamburger.addEventListener('click', function () {
        menu.classList.toggle('show');
    });

    // Fecha o menu automaticamente quando um link for clicado
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            menu.classList.remove('show'); // Remove a classe 'show' para fechar o menu
        });
    });

    // Script para rolar suavemente ao clicar nos links da barra de navegação
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
