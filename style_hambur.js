const menus = document.getElementById('menus');
const menu = document.getElementById('menu');

menus.addEventListener('click', () => {
    menus.classList.toggle('active');
    menu.classList.toggle('active');
});
