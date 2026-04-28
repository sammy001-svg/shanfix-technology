// ===================================
// SHANFIX TECHNOLOGY - MAIN JAVASCRIPT
// ===================================

// ===================================
// HERO CAROUSEL
// ===================================

let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const indicators = document.querySelectorAll('.carousel-indicators .indicator');
const totalSlides = slides.length;
let autoplayInterval;

// Function to show specific slide
function showSlide(index) {
  // Remove active class from all slides and indicators
  slides.forEach(slide => slide.classList.remove('active'));
  indicators.forEach(indicator => indicator.classList.remove('active'));
  
  // Add active class to current slide and indicator
  slides[index].classList.add('active');
  indicators[index].classList.add('active');
  
  currentSlide = index;
}

// Function to go to next slide
function nextSlide() {
  let next = (currentSlide + 1) % totalSlides;
  showSlide(next);
}

// Function to go to previous slide
function prevSlide() {
  let prev = (currentSlide - 1 + totalSlides) % totalSlides;
  showSlide(prev);
}

// Autoplay carousel every 5 seconds
function startAutoplay() {
  autoplayInterval = setInterval(nextSlide, 5000);
}

function stopAutoplay() {
  clearInterval(autoplayInterval);
}

// Initialize carousel
startAutoplay();

// Pause autoplay on hover
const heroCarousel = document.querySelector('.hero-carousel');
if (heroCarousel) {
  heroCarousel.addEventListener('mouseenter', stopAutoplay);
  heroCarousel.addEventListener('mouseleave', startAutoplay);
}

// Navigation controls
const prevButton = document.querySelector('.carousel-control.prev');
const nextButton = document.querySelector('.carousel-control.next');

if (prevButton) {
  prevButton.addEventListener('click', () => {
    prevSlide();
    stopAutoplay();
    startAutoplay(); // Restart autoplay after manual navigation
  });
}

if (nextButton) {
  nextButton.addEventListener('click', () => {
    nextSlide();
    stopAutoplay();
    startAutoplay(); // Restart autoplay after manual navigation
  });
}

// Indicator navigation
indicators.forEach((indicator, index) => {
  indicator.addEventListener('click', () => {
    showSlide(index);
    stopAutoplay();
    startAutoplay(); // Restart autoplay after manual navigation
  });
});

// Smooth Scroll Navigation
document.querySelectorAll('a[href*="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    
    // Check if it's purely a fragment on the current page OR if it's for current page
    if (href.startsWith('#') || href.startsWith('index.php#')) {
      const fragment = href.includes('#') ? '#' + href.split('#')[1] : null;
      const target = fragment ? document.querySelector(fragment) : null;
      
      if (target) {
        e.preventDefault();
        const navHeight = document.querySelector('.navbar').offsetHeight;
        const targetPosition = target.offsetTop - navHeight;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
      // If it's index.php#ButNotThisPage, let the link follow normally
    }
  });
});

// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const navMenu = document.getElementById('navMenu');

if (mobileMenuToggle) {
  mobileMenuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    mobileMenuToggle.classList.toggle('active');
  });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    navMenu.classList.remove('active');
    mobileMenuToggle.classList.remove('active');
  });
});

// Mega Menu Mobile Toggle
const navItemDropdown = document.querySelector('.nav-item-dropdown');

if (navItemDropdown && window.innerWidth <= 767) {
  const dropdownLink = navItemDropdown.querySelector('.nav-link');
  
  dropdownLink.addEventListener('click', (e) => {
    e.preventDefault();
    navItemDropdown.classList.toggle('active');
  });
}

// Update mega menu behavior on window resize
window.addEventListener('resize', () => {
  const navItemDropdown = document.querySelector('.nav-item-dropdown');
  if (window.innerWidth > 767 && navItemDropdown) {
    navItemDropdown.classList.remove('active');
  }
});


// Navbar Scroll Effect
let lastScroll = 0;
const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
  const currentScroll = window.pageYOffset;
  
  if (currentScroll > 100) {
    navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
  } else {
    navbar.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
  }
  
  lastScroll = currentScroll;
});

// Scroll Animation Observer
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('aos-animate');
    }
  });
}, observerOptions);

// Observe all elements with data-aos attribute
document.querySelectorAll('[data-aos]').forEach(el => {
  observer.observe(el);
});

// Contact Form Handling
const contactForm = document.getElementById('contactForm');

if (contactForm) {
  contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Get form values
    const formData = {
      name: document.getElementById('name').value,
      email: document.getElementById('email').value,
      service: document.getElementById('service').value,
      message: document.getElementById('message').value
    };
    
    // Here you would typically send the data to a server
    console.log('Form submitted:', formData);
    
    // Show success message (you can customize this)
    alert('Thank you for your message! We will get back to you soon.');
    
    // Reset form
    contactForm.reset();
  });
}

// Add active state to navigation links based on scroll position
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link');

function highlightNavigation() {
  const scrollY = window.pageYOffset;
  
  sections.forEach(section => {
    const sectionHeight = section.offsetHeight;
    const sectionTop = section.offsetTop - 100;
    const sectionId = section.getAttribute('id');
    
    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${sectionId}`) {
          link.classList.add('active');
        }
      });
    }
  });
}

window.addEventListener('scroll', highlightNavigation);

// Parallax effect for hero shapes
window.addEventListener('scroll', () => {
  const scrolled = window.pageYOffset;
  const shapes = document.querySelectorAll('.hero-shape');
  
  shapes.forEach((shape, index) => {
    const speed = (index + 1) * 0.1;
    shape.style.transform = `translateY(${scrolled * speed}px)`;
  });
});

// Animation for Skill Progress Bars
const skillBars = document.querySelectorAll('.skill-progress');
const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const targetWidth = entry.target.dataset.width;
            if (targetWidth) {
                entry.target.style.width = targetWidth + '%';
            }
            skillObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.2 });

skillBars.forEach(bar => {
    // Get the width from the style attribute if it's there
    let targetWidth = bar.getAttribute('style');
    if (targetWidth && targetWidth.includes('width:')) {
        const match = targetWidth.match(/width:\s*(\d+)%/);
        if (match) {
            bar.dataset.width = match[1];
        }
    }
    bar.style.width = '0%'; // Start at 0
    skillObserver.observe(bar);
});

// Add hover effect to service cards
const serviceCards = document.querySelectorAll('.service-card');

serviceCards.forEach(card => {
  card.addEventListener('mouseenter', function() {
    this.style.borderColor = 'var(--color-primary)';
  });
  
  card.addEventListener('mouseleave', function() {
    this.style.borderColor = 'transparent';
  });
});

// Console welcome message
console.log('%cShanfix Technology', 'font-size: 24px; font-weight: bold; color: #22c55e;');
console.log('%cWebsite built with modern web technologies', 'font-size: 14px; color: #6b7280;');

// ===================================
// ADVERT CAROUSEL
// ===================================

const advertCarousel = document.querySelector('.advert-carousel');
if (advertCarousel) {
  let currentAdvertSlide = 0;
  const advertSlides = advertCarousel.querySelectorAll('.advert-slide');
  const advertIndicators = advertCarousel.querySelectorAll('.advert-indicator');
  const totalAdvertSlides = advertSlides.length;
  let advertAutoplayInterval;

  // Function to show specific slide
  function showAdvertSlide(index) {
    // Remove active class from all slides and indicators
    advertSlides.forEach(slide => slide.classList.remove('active'));
    advertIndicators.forEach(indicator => indicator.classList.remove('active'));
    
    // Add active class to current slide and indicator
    if (advertSlides[index]) advertSlides[index].classList.add('active');
    if (advertIndicators[index]) advertIndicators[index].classList.add('active');
    
    currentAdvertSlide = index;
  }

  // Function to go to next slide
  function nextAdvertSlide() {
    let next = (currentAdvertSlide + 1) % totalAdvertSlides;
    showAdvertSlide(next);
  }

  // Function to go to previous slide
  function prevAdvertSlide() {
    let prev = (currentAdvertSlide - 1 + totalAdvertSlides) % totalAdvertSlides;
    showAdvertSlide(prev);
  }

  // Autoplay carousel every 6 seconds (slightly different than hero to avoid sync)
  function startAdvertAutoplay() {
    advertAutoplayInterval = setInterval(nextAdvertSlide, 6000);
  }

  function stopAdvertAutoplay() {
    clearInterval(advertAutoplayInterval);
  }

  // Initialize carousel
  startAdvertAutoplay();

  // Pause autoplay on hover
  advertCarousel.addEventListener('mouseenter', stopAdvertAutoplay);
  advertCarousel.addEventListener('mouseleave', startAdvertAutoplay);

  // Navigation controls
  const advertPrevButton = advertCarousel.querySelector('.advert-control.prev');
  const advertNextButton = advertCarousel.querySelector('.advert-control.next');

  if (advertPrevButton) {
    advertPrevButton.addEventListener('click', () => {
      prevAdvertSlide();
      stopAdvertAutoplay();
      startAdvertAutoplay();
    });
  }

  if (advertNextButton) {
    advertNextButton.addEventListener('click', () => {
      nextAdvertSlide();
      stopAdvertAutoplay();
      startAdvertAutoplay();
    });
  }

  // Indicator navigation
  advertIndicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
      showAdvertSlide(index);
      stopAdvertAutoplay();
      startAdvertAutoplay();
    });
  });
}

// ===================================
// ORDER MODAL LOGIC
// ===================================

const orderModal = document.getElementById('orderModal');
const closeModalBtn = document.getElementById('closeModal');
const buyBtns = document.querySelectorAll('.open-order-modal');
const qtyInput = document.getElementById('qtyInput');
const qtyPlus = document.getElementById('qtyPlus');
const qtyMinus = document.getElementById('qtyMinus');
const placeOrderBtn = document.getElementById('placeOrderBtn');
const successOverlay = document.getElementById('successOverlay');
const continueShoppingBtn = document.getElementById('continueShopping');

// Modal elements to populate
const modalImage = document.getElementById('modalImage');
const modalTitle = document.getElementById('modalTitle');
const modalPrice = document.getElementById('modalPrice');
const modalDescription = document.getElementById('modalDescription');

if (orderModal) {
  // Open Modal
  buyBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const card = this.closest('.product-card');
      const name = card.dataset.productName;
      const price = card.dataset.productPrice;
      const image = card.dataset.productImage;
      const description = card.dataset.productDescription;

      modalTitle.textContent = name;
      modalPrice.innerHTML = `<span>KES</span> ${price}`;
      modalImage.src = image;
      modalImage.alt = name;
      modalDescription.textContent = description;
      
      qtyInput.value = 1;
      orderModal.classList.add('active');
      document.body.style.overflow = 'hidden'; // Stop background scroll
    });
  });

  // Close Modal
  function closeOrderModal() {
    orderModal.classList.remove('active');
    successOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  closeModalBtn.addEventListener('click', closeOrderModal);

  // Close on backdrop click
  orderModal.addEventListener('click', (e) => {
    if (e.target === orderModal) closeOrderModal();
  });

  // Quantity Controls
  if (qtyPlus) {
    qtyPlus.addEventListener('click', () => {
      qtyInput.value = parseInt(qtyInput.value) + 1;
    });
  }

  if (qtyMinus) {
    qtyMinus.addEventListener('click', () => {
      if (parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
      }
    });
  }

  // Place Order Simulation
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', () => {
      successOverlay.classList.add('active');
    });
  }

  if (continueShoppingBtn) {
    continueShoppingBtn.addEventListener('click', closeOrderModal);
  }
}
