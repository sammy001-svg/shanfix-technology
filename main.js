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

  // Handle Place Order Simulation
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', () => {
      successOverlay.classList.add('active');
    });
  }

  if (continueShoppingBtn) {
    continueShoppingBtn.addEventListener('click', closeOrderModal);
  }
}

// ===================================
// DYNAMIC CATEGORIZED CATALOG
// ===================================

async function renderCategorizedCatalog() {
    const catalogContainer = document.getElementById('dynamicCatalog');
    if (!catalogContainer) return;

    // Show loading state
    catalogContainer.innerHTML = `
        <div class="loading-catalog" style="text-align: center; padding: 50px;">
            <div class="spinner" style="margin-bottom: 20px;"><i class="fas fa-circle-notch fa-spin fa-3x"></i></div>
            <p>Loading our premium catalog...</p>
        </div>
    `;

    try {
        // Fetch categories and products in parallel
        const [catRes, prodRes] = await Promise.all([
            fetch('admin/api/categories.php'),
            fetch('admin/api/products.php')
        ]);

        const catData = await catRes.json();
        const prodData = await prodRes.json();

        if (!catData.success || !prodData.success) {
            throw new Error('Failed to load catalog data');
        }

        const categories = catData.categories.map(c => c.name);
        const products = prodData.products;

        if (products.length === 0) {
            catalogContainer.innerHTML = `
                <div class="empty-catalog" data-aos="fade-up">
                    <i class="fas fa-box-open"></i>
                    <p>Our premium catalog is currently being updated. Please check back soon!</p>
                </div>
            `;
            return;
        }

        // Group products by category name
        const grouped = {};
        categories.forEach(cat => grouped[cat] = []);
        
        products.forEach(p => {
            const catName = p.category_name;
            if (!grouped[catName]) grouped[catName] = [];
            grouped[catName].push(p);
        });

        // Generate HTML
        let catalogHtml = '';
        categories.forEach(cat => {
            const catProducts = grouped[cat];
            if (catProducts && catProducts.length > 0) {
                catalogHtml += `
                    <div class="catalog-category-section" data-aos="fade-up">
                        <h2 class="category-heading">${cat}</h2>
                        <div class="product-grid">
                            ${catProducts.map(p => `
                                <div class="product-card" data-aos="fade-up"
                                     data-product-name="${p.name}" 
                                     data-product-price="${p.price}" 
                                     data-product-image="${p.image_url}"
                                     data-product-description="${p.description}">
                                    <div class="product-image-wrapper">
                                        <img src="${p.image_url}" alt="${p.name}" class="product-image" onerror="this.src='assets/img/placeholder.jpg'">
                                        <div class="product-overlay">
                                            <div class="overlay-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <span class="product-category">${cat}</span>
                                        <h3 class="product-title">${p.name}</h3>
                                        <p class="product-description">${(p.description || '').substring(0, 100)}${(p.description || '').length > 100 ? '...' : ''}</p>
                                        <div class="product-footer">
                                            <div class="product-price"><span>KES</span> ${p.price}</div>
                                            <button class="btn btn-primary btn-buy open-order-modal">Order Now</button>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
        });

        if (catalogHtml === '') {
             catalogContainer.innerHTML = `
                <div class="empty-catalog" data-aos="fade-up">
                    <i class="fas fa-box-open"></i>
                    <p>No products available in the selected categories.</p>
                </div>
            `;
        } else {
            catalogContainer.innerHTML = catalogHtml;
        }

        // Re-initialize Order Modal triggers for dynamic content
        initOrderModalTriggers();
        
        // Re-initialize AOS for new elements
        if (window.AOS) {
            AOS.refresh();
        }

    } catch (error) {
        console.error('Catalog loading error:', error);
        catalogContainer.innerHTML = `
            <div class="error-catalog" style="text-align: center; padding: 50px; color: #dc3545;">
                <i class="fas fa-exclamation-triangle fa-3x" style="margin-bottom: 20px;"></i>
                <p>Unable to load the catalog. Please try refreshing the page.</p>
            </div>
        `;
    }
}

function initOrderModalTriggers() {
    const buyBtns = document.querySelectorAll('.open-order-modal');
    buyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const name = card.dataset.productName;
            const price = card.dataset.productPrice;
            const image = card.dataset.productImage;
            const description = card.dataset.productDescription;

            const modalTitle = document.getElementById('modalTitle');
            const modalPrice = document.getElementById('modalPrice');
            const modalImage = document.getElementById('modalImage');
            const modalDescription = document.getElementById('modalDescription');
            const orderModal = document.getElementById('orderModal');
            const qtyInput = document.getElementById('qtyInput');

            if (modalTitle) modalTitle.textContent = name;
            if (modalPrice) modalPrice.innerHTML = `<span>KES</span> ${price}`;
            if (modalImage) {
                modalImage.src = image;
                modalImage.alt = name;
            }
            if (modalDescription) modalDescription.textContent = description;
            
            if (qtyInput) qtyInput.value = 1;
            if (orderModal) {
                orderModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
}

// Initialize dynamic catalog if container exists
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('dynamicCatalog')) {
        renderCategorizedCatalog();
    }
});
