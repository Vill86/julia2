// ============================================
//   ПОЛНЫЙ JAVASCRIPT КОД
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    //   МОБИЛЬНОЕ МЕНЮ
    // ============================================
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
    
    // ============================================
    //   ПЛАВНАЯ ПРОКРУТКА
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Закрываем мобильное меню
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    });
    
    // ============================================
    //   ТАБЫ УСЛУГ
    // ============================================
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-tab');
            
            // Удаляем активный класс
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Добавляем активный класс
            button.classList.add('active');
            const activeTab = document.getElementById(tabName);
            if (activeTab) {
                activeTab.classList.add('active');
            }
        });
    });
    
    // ============================================
    //   КНОПКИ ЗАПИСИ
    // ============================================
    const bookButtons = document.querySelectorAll('.btn-book');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Получаем название услуги
            const serviceName = this.getAttribute('data-service');
            
            // Прокручиваем к форме
            const contactSection = document.getElementById('contact');
            if (contactSection) {
                contactSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            
            // Заполняем поле услуги
            setTimeout(() => {
                const serviceSelect = document.getElementById('service');
                if (serviceSelect && serviceName) {
                    // Ищем подходящую опцию
                    for (let option of serviceSelect.options) {
                        if (option.text.includes(serviceName.split(' ')[0])) {
                            serviceSelect.value = option.value;
                            break;
                        }
                    }
                }
            }, 1000);
        });
    });
    
    // ============================================
    //   ОБРАБОТКА ФОРМЫ
    // ============================================
    const bookingForm = document.getElementById('booking-form');
    
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Получаем данные формы
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value || 'Не указан',
                service: document.getElementById('service').options[document.getElementById('service').selectedIndex].text,
                message: document.getElementById('message').value || 'Нет сообщения'
            };
            
            // Показываем модальное окно успеха
            showSuccessModal(formData);
            
            // Очищаем форму
            this.reset();
        });
    }
    
    // ============================================
    //   МОДАЛЬНОЕ ОКНО УСПЕХА
    // ============================================
    function showSuccessModal(data) {
        // Создаем оверлей
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Создаем модальное окно
        const modal = document.createElement('div');
        modal.style.cssText = `
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: relative;
        `;
        
        modal.innerHTML = `
            <h2 style="color: #8b5cf6; margin-bottom: 1rem;">✅ Заявка отправлена!</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Спасибо, ${data.name}! Я свяжусь с вами в течение 24 часов.
            </p>
            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Услуга: <strong>${data.service}</strong><br>
                Телефон: <strong>${data.phone}</strong>
            </p>
            <button onclick="this.closest('div').parentElement.remove()" style="
                background: #8b5cf6;
                color: white;
                border: none;
                padding: 0.75rem 2rem;
                border-radius: 50px;
                cursor: pointer;
                font-size: 1rem;
                font-weight: 600;
            ">Закрыть</button>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Закрытие по клику на оверлей
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.remove();
            }
        });
        
        // Автоматическое закрытие через 5 секунд
        setTimeout(() => {
            if (overlay.parentElement) {
                overlay.remove();
            }
        }, 5000);
    }
    
    // ============================================
    //   МАСКА ТЕЛЕФОНА
    // ============================================
    const phoneInput = document.getElementById('phone');
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            
            if (value.length > 0) {
                if (value[0] === '7' || value[0] === '8') {
                    value = value.substring(1);
                }
                formattedValue = '+7';
                if (value.length > 0) {
                    formattedValue += ' (' + value.substring(0, 3);
                }
                if (value.length > 3) {
                    formattedValue += ') ' + value.substring(3, 6);
                }
                if (value.length > 6) {
                    formattedValue += '-' + value.substring(6, 8);
                }
                if (value.length > 8) {
                    formattedValue += '-' + value.substring(8, 10);
                }
            }
            
            e.target.value = formattedValue;
        });
    }
    
    // ============================================
    //   АКТИВНАЯ ССЫЛКА В МЕНЮ
    // ============================================
    window.addEventListener('scroll', () => {
        let current = '';
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
    
    console.log('✅ Сайт успешно загружен!');
});
