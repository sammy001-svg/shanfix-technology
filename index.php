<?php include 'includes/header.php'; ?>

    <!-- Hero Section with Carousel -->
    <section class="hero" id="home">
      <div class="hero-carousel">
        <!-- Slide 1 -->
        <div class="hero-slide active">
          <div class="hero-slide-bg hero-bg-1"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Innovative Technology Solutions for
                <span class="highlight">Your Business</span>
              </h1>
              <p class="hero-subtitle">
                From web development to event management, we provide comprehensive
                IT and business solutions in Nairobi, Kenya
              </p>
              <div class="hero-buttons">
                <a href="#services" class="btn btn-primary">Explore Services</a>
                <a href="contact.php" class="btn btn-secondary">Get in Touch</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide">
          <div class="hero-slide-bg hero-bg-2"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Transform Your Digital Presence with
                <span class="highlight">Expert Solutions</span>
              </h1>
              <p class="hero-subtitle">
                Professional web development, hosting, and digital marketing services
                tailored to your business needs
              </p>
              <div class="hero-buttons">
                <a href="#services" class="btn btn-primary">Our Services</a>
                <a href="#about" class="btn btn-secondary">Learn More</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide">
          <div class="hero-slide-bg hero-bg-3"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Empowering Businesses Through
                <span class="highlight">Technology</span>
              </h1>
              <p class="hero-subtitle">
                Networking, software solutions, and IT infrastructure services
                to drive your business forward
              </p>
              <div class="hero-buttons">
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
                <a href="#services" class="btn btn-secondary">View Services</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control prev" aria-label="Previous slide">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <button class="carousel-control next" aria-label="Next slide">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
          <button class="indicator active" data-slide="0" aria-label="Go to slide 1"></button>
          <button class="indicator" data-slide="1" aria-label="Go to slide 2"></button>
          <button class="indicator" data-slide="2" aria-label="Go to slide 3"></button>
        </div>
      </div>
    </section>

    <!-- Feature Cards Section -->
    <section class="feature-cards-section">
      <div class="container">
        <div class="feature-cards-grid">
          <!-- Web Development Card -->
          <div class="feature-card">
            <div class="feature-card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
              </svg>
            </div>
            <h3 class="feature-card-title">Web Development</h3>
            <p class="feature-card-description">
              Custom websites and web applications built with modern technologies to help your business thrive online.
            </p>
            <a href="web-development.php" class="feature-card-link">
              Learn More
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>

          <!-- Graphics Design Card -->
          <div class="feature-card">
            <div class="feature-card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                <path d="M2 2l7.586 7.586"></path>
                <circle cx="11" cy="11" r="2"></circle>
              </svg>
            </div>
            <h3 class="feature-card-title">Graphics Design</h3>
            <p class="feature-card-description">
              Creative and professional graphic design services to elevate your brand identity and visual communication.
            </p>
            <a href="printing-branding.php" class="feature-card-link">
              Learn More
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>

          <!-- Printing & Branding Card -->
          <div class="feature-card">
            <div class="feature-card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>
            </div>
            <h3 class="feature-card-title">Printing & Branding</h3>
            <p class="feature-card-description">
              High-quality printing services and comprehensive branding solutions to make your business stand out.
            </p>
            <a href="printing-branding.php" class="feature-card-link">
              Learn More
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>




    <!-- Advertisement Carousel Section -->
    <section class="advert-carousel-section">
      <div class="container">
        <div class="advert-carousel">
          <!-- Banner 1 -->
          <div class="advert-slide active">
            <img src="assets/Banners-1.jpg" alt="Bulk SMS Sender Advertisement" class="advert-image">
          </div>

          <!-- Banner 2 -->
          <div class="advert-slide">
            <img src="assets/Banners-2.jpg" alt="Ring Back Tone Advertisement" class="advert-image">
          </div>

          <!-- Navigation Arrows -->
          <button class="advert-control prev" aria-label="Previous advertisement">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button class="advert-control next" aria-label="Next advertisement">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <!-- Carousel Indicators -->
          <div class="advert-indicators">
            <button class="advert-indicator active" data-slide="0" aria-label="Go to advertisement 1"></button>
            <button class="advert-indicator" data-slide="1" aria-label="Go to advertisement 2"></button>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Our Services</h2>
          <p class="section-subtitle">
            Comprehensive solutions tailored to your business needs
          </p>
        </div>
        <div class="services-grid">
          <!-- App Development -->
          <div class="service-card" data-aos="fade-up">
            <div class="service-image-container">
              <img src="assets/App-development-jpeg.jpg" alt="App Development" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">App Development</h3>
              <p class="service-description">
                Custom mobile applications for iOS and Android
              </p>
              <a href="app-development.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Web Development -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="100">
            <div class="service-image-container">
              <img src="assets/web-development-jpeg.jpg" alt="Web Development" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Web Development</h3>
              <p class="service-description">
                Modern, responsive websites that drive results
              </p>
              <a href="web-development.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Web Hosting -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="200">
            <div class="service-image-container">
              <img src="assets/web-hosting-jpeg.jpg" alt="Web Hosting" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Web Hosting</h3>
              <p class="service-description">
                Reliable and secure hosting solutions for your business
              </p>
              <a href="web-hosting.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Software Solution -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="300">
            <div class="service-image-container">
              <img src="assets/Software-solution-jpeg.jpg" alt="Software Solution" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Software Solution</h3>
              <p class="service-description">
                Tailored software to streamline your operations
              </p>
              <a href="software-solution.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Networking Solution -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="400">
            <div class="service-image-container">
              <img src="assets/networking-solution-jpeg.jpg" alt="Networking Solution" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Networking Solution</h3>
              <p class="service-description">
                Robust network infrastructure setup and maintenance
              </p>
              <a href="networking-solution.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Digital Marketing -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="500">
            <div class="service-image-container">
              <img src="assets/digital-marketing-jpeg.jpg" alt="Digital Marketing" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Digital Marketing</h3>
              <p class="service-description">
                Strategies to grow your brand's online presence
              </p>
              <a href="digital-marketing.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Bulk SMS -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="600">
            <div class="service-image-container">
              <img src="assets/Bulk-sms-jpeg.jpg" alt="Bulk SMS" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Bulk SMS</h3>
              <p class="service-description">
                Effective mass communication tools for outreach
              </p>
              <a href="bulk-sms.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- SEO Boost -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="700">
            <div class="service-image-container">
              <img src="assets/SEO-boost-jpeg.jpg" alt="SEO Boost" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">SEO Boost</h3>
              <p class="service-description">
                Optimize your site to rank higher on search engines
              </p>
              <a href="seo-boost.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Event management -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="800">
            <div class="service-image-container">
              <img src="assets/event-management-jpeg.jpg" alt="Event Management" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Event Management</h3>
              <p class="service-description">
                Professional planning for memorable corporate events
              </p>
              <a href="event-management.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Event ticketing -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="900">
            <div class="service-image-container">
              <img src="assets/event-ticketing-jpeg.jpg" alt="Event Ticketing" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Event Ticketing</h3>
              <p class="service-description">
                Seamless ticketing solutions for your events
              </p>
              <a href="event-ticketing.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Printing & branding -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="1000">
            <div class="service-image-container">
              <img src="assets/printing-brabdibg-jpeg.jpg" alt="Printing & Branding" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Printing & Branding</h3>
              <p class="service-description">
                High-quality print materials and brand assets
              </p>
              <a href="printing-branding.php" class="service-btn">Get Started</a>
            </div>
          </div>

          <!-- Consultancy -->
          <div class="service-card" data-aos="fade-up" data-aos-delay="1100">
            <div class="service-image-container">
              <img src="assets/service-4.jpg" alt="Consultancy" class="service-image" />
            </div>
            <div class="service-content">
              <h3 class="service-title">Consultancy</h3>
              <p class="service-description">
                Expert advice to guide your technology decisions
              </p>
              <a href="consultancy.php" class="service-btn">Get Started</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php include 'includes/footer.php'; ?>



