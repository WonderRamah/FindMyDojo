/**
 * FindMyDojo - Animations JavaScript File
 * Handles AOS initialization and custom animations
 */

document.addEventListener('DOMContentLoaded', function() {
  
  // ===== INITIALIZE AOS (Animate On Scroll) =====
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      easing: 'ease-out',
      once: true,
      offset: 100,
      disable: function() {
        // Disable on mobile if preferred
        return window.innerWidth < 768;
      }
    });
    
    // Refresh AOS on window resize (debounced)
    window.addEventListener('resize', debounce(function() {
      AOS.refresh();
    }, 250));
  }
  
  // ===== PARALLAX EFFECT FOR HERO SECTION =====
  const heroSection = document.querySelector('.hero-section');
  if (heroSection) {
    window.addEventListener('scroll', function() {
      const scrolled = window.pageYOffset;
      const parallaxSpeed = 0.5;
      
      if (scrolled < window.innerHeight) {
        heroSection.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
      }
    });
  }
  
  // ===== COUNTER ANIMATION =====
  const counters = document.querySelectorAll('[data-counter]');
  
  const animateCounter = (counter) => {
    const target = parseInt(counter.getAttribute('data-counter'));
    const duration = 2000; // 2 seconds
    const increment = target / (duration / 16); // 60fps
    let current = 0;
    
    const updateCounter = () => {
      current += increment;
      if (current < target) {
        counter.textContent = Math.floor(current).toLocaleString();
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = target.toLocaleString();
      }
    };
    
    updateCounter();
  };
  
  // Intersection Observer for counters
  if (counters.length > 0) {
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
          animateCounter(entry.target);
          entry.target.classList.add('counted');
        }
      });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => counterObserver.observe(counter));
  }
  
  // ===== STAGGER ANIMATION FOR CARDS =====
  const cardContainers = document.querySelectorAll('[data-stagger-cards]');
  
  cardContainers.forEach(container => {
    const cards = container.querySelectorAll('.dojo-card, .feature-card, .testimonial-card');
    
    cards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.1}s`;
      card.classList.add('fade-in-up');
    });
  });
  
  // ===== HOVER TILT EFFECT =====
  const tiltElements = document.querySelectorAll('[data-tilt]');
  
  tiltElements.forEach(element => {
    element.addEventListener('mousemove', function(e) {
      const rect = element.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      
      const rotateX = (y - centerY) / 10;
      const rotateY = (centerX - x) / 10;
      
      element.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
    });
    
    element.addEventListener('mouseleave', function() {
      element.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
    });
  });
  
  // ===== REVEAL ON SCROLL =====
  const revealElements = document.querySelectorAll('[data-reveal]');
  
  if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
        }
      });
    }, { threshold: 0.15 });
    
    revealElements.forEach(element => {
      element.classList.add('reveal-hidden');
      revealObserver.observe(element);
    });
  }
  
  // ===== TYPING ANIMATION =====
  const typeElements = document.querySelectorAll('[data-type]');
  
  typeElements.forEach(element => {
    const text = element.getAttribute('data-type');
    const speed = parseInt(element.getAttribute('data-type-speed')) || 100;
    let index = 0;
    
    element.textContent = '';
    element.style.borderRight = '2px solid var(--color-primary)';
    
    const typeChar = () => {
      if (index < text.length) {
        element.textContent += text.charAt(index);
        index++;
        setTimeout(typeChar, speed);
      } else {
        // Blinking cursor effect
        setInterval(() => {
          element.style.borderRight = element.style.borderRight === 'none' 
            ? '2px solid var(--color-primary)' 
            : 'none';
        }, 500);
      }
    };
    
    // Start typing when element is in view
    const typeObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && index === 0) {
          setTimeout(typeChar, 500);
        }
      });
    }, { threshold: 0.5 });
    
    typeObserver.observe(element);
  });
  
  // ===== PROGRESS BAR ANIMATION =====
  const progressBars = document.querySelectorAll('[data-progress]');
  
  if (progressBars.length > 0) {
    const progressObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
          const progress = entry.target.getAttribute('data-progress');
          const bar = entry.target.querySelector('.progress-bar-fill');
          
          if (bar) {
            setTimeout(() => {
              bar.style.width = progress + '%';
            }, 200);
          }
          
          entry.target.classList.add('animated');
        }
      });
    }, { threshold: 0.5 });
    
    progressBars.forEach(bar => progressObserver.observe(bar));
  }
  
  console.log('Animations initialized! ✨');
});

// Add CSS for custom animations
const animationStyles = document.createElement('style');
animationStyles.textContent = `
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
  }
  
  .reveal-hidden {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
  }
  
  .reveal-hidden.revealed {
    opacity: 1;
    transform: translateY(0);
  }
  
  [data-tilt] {
    transition: transform 0.3s ease-out;
    transform-style: preserve-3d;
  }
  
  .progress-bar {
    width: 100%;
    height: 8px;
    background: var(--color-muted);
    border-radius: var(--radius-full);
    overflow: hidden;
  }
  
  .progress-bar-fill {
    height: 100%;
    background: var(--gradient-button);
    width: 0;
    transition: width 1.5s ease-out;
    border-radius: var(--radius-full);
  }
`;
document.head.appendChild(animationStyles);