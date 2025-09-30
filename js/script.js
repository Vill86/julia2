// Плавная прокрутка
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Мобильное меню
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Табы услуг
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const tabName = button.getAttribute('data-tab');
        
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        button.classList.add('active');
        const activeTab = document.getElementById(tabName);
        if (activeTab) activeTab.classList.add('active');
    });
});

// Форма
const form = document.getElementById('booking-form');
if (form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Спасибо за заявку! Мы свяжемся с вами в ближайшее время.');
        form.reset();
    });
}

console.log('Сайт загружен успешно!');
