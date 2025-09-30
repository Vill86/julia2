// Плавная прокрутка к секциям
function scrollToContact() {
    const contactSection = document.getElementById('contact');
    contactSection.scrollIntoView({ behavior: 'smooth' });
}

// Обработка всех ссылок навигации
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Обработка формы записи
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Формирование письма
    const subject = encodeURIComponent('Заявка на консультацию от ' + data.name);
    const body = encodeURIComponent(
        `Имя: ${data.name}\n` +
        `Email: ${data.email}\n` +
        `Телефон: ${data.phone}\n` +
        `Услуга: ${getServiceName(data.service)}\n` +
        `Сообщение: ${data.message || 'Не указано'}`
    );
    
    // Замените на реальный email
    const email = 'julia.psycholog@example.com';
    
    // Открытие почтового клиента
    window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
    
    // Показ сообщения об успехе
    showMessage('Заявка отправлена! Проверьте ваш почтовый клиент.', 'success');
    
    // Очистка формы
    this.reset();
});

// Вспомогательная функция для получения названия услуги
function getServiceName(value) {
    const services = {
        'psychology': 'Психологическое консультирование',
        'sexology': 'Сексология',
        'energy': 'Энерготерапия',
        'body': 'Телесная терапия'
    };
    return services[value] || value;
}

// Показ сообщений формы
function showMessage(text, type) {
    const messageDiv = document.getElementById('formMessage');
    messageDiv.textContent = text;
    messageDiv.className = `form-message ${type}`;
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Анимация появления элементов при скролле
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Применение анимации к карточкам
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.service-card, .review-card, .credential-item');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
