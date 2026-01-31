// ==================== SCROLL REVEAL ANIMATION ====================
const reveals = document.querySelectorAll(".reveal");

function revealOnScroll() {
    const windowHeight = window.innerHeight;
    const revealPoint = 100;

    reveals.forEach(el => {
        const elementTop = el.getBoundingClientRect().top;
        
        if (elementTop < windowHeight - revealPoint) {
            el.classList.add("active");
        }
    });
}

// Initial check
window.addEventListener("DOMContentLoaded", revealOnScroll);

// On scroll
window.addEventListener("scroll", revealOnScroll);

// ==================== NAVBAR SCROLL EFFECT ====================
const navbar = document.querySelector('.navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    // Add shadow when scrolled
    if (currentScroll > 50) {
        navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
    } else {
        navbar.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    }
    
    lastScroll = currentScroll;
});

// ==================== SMOOTH SCROLL FOR ANCHOR LINKS ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target) {
            const offsetTop = target.offsetTop - 80; // Account for navbar height
            
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ==================== ANIMATE NUMBERS ON SCROLL ====================
function animateNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const text = stat.textContent.trim();
        
        // Check if it contains a number
        const matches = text.match(/[\d,]+/);
        if (!matches) return;
        
        const numberStr = matches[0].replace(/,/g, '');
        const target = parseInt(numberStr);
        
        if (isNaN(target)) return;
        
        // Check if element is in viewport
        const rect = stat.getBoundingClientRect();
        const isInView = rect.top < window.innerHeight && rect.bottom >= 0;
        
        if (isInView && !stat.classList.contains('animated')) {
            stat.classList.add('animated');
            
            // Animate the number
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                // Format based on original text
                if (text.includes('mil')) {
                    stat.textContent = Math.floor(current).toLocaleString('pt-BR') + ' mil+';
                } else if (text.includes('R$')) {
                    stat.textContent = 'R$ ' + Math.floor(current) + ' mil';
                } else if (text.includes('anos')) {
                    stat.textContent = Math.floor(current) + ' anos';
                } else {
                    stat.textContent = text.replace(/[\d,]+/, Math.floor(current).toLocaleString('pt-BR'));
                }
            }, 30);
        }
    });
}

window.addEventListener('scroll', animateNumbers);
window.addEventListener('DOMContentLoaded', animateNumbers);

// ==================== PARALLAX EFFECT FOR HERO ====================
const hero = document.querySelector('.hero');
if (hero) {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroContent = document.querySelector('.hero-content');
        
        if (heroContent && scrolled < window.innerHeight) {
            heroContent.style.transform = `translateY(${scrolled * 0.5}px)`;
            heroContent.style.opacity = 1 - (scrolled / window.innerHeight);
        }
    });
}

// ==================== ADD LOADING ANIMATION ====================
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
});

// ==================== INTERSECTION OBSERVER FOR BETTER PERFORMANCE ====================
if ('IntersectionObserver' in window) {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                // Optional: stop observing after animation
                // observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all reveal elements
    reveals.forEach(el => observer.observe(el));
}

// ==================== ADD HOVER EFFECT TO CARDS ====================
const cards = document.querySelectorAll('.team-card, .impact-card, .card');

cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    });
});

// ==================== PREVENT ANIMATIONS ON PAGE LOAD ====================
window.addEventListener('load', () => {
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 100);
});

// ==================== LAZY LOAD IMAGES ====================
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

console.log('🚀 PMN Website loaded successfully!');