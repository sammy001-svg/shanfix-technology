<?php include 'includes/header.php'; ?>
    <!-- Hero Section with Carousel -->
    <section class="hero printing-hero" id="home">
      <div class="hero-carousel print-carousel">
        <!-- Slide 1 -->
        <div class="hero-slide active">
          <div class="hero-slide-bg print-bg-1" style="background: url('assets/print-slide-1.png') no-repeat center center; background-size: cover;"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Precision <span>Printing Solutions</span>
              </h1>
              <p class="hero-subtitle">
                Delivering high-quality, professional print materials that make your business stand out from the crowd.
              </p>
              <div class="hero-buttons">
                <a href="#products" class="btn btn-primary">View Catalog</a>
                <a href="contact.php" class="btn btn-secondary">Get a Quote</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide">
          <div class="hero-slide-bg print-bg-2" style="background: url('assets/print-slide-2.png') no-repeat center center; background-size: cover;"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Elite <span>Corporate Branding</span>
              </h1>
              <p class="hero-subtitle">
                Transform your brand identity with premium business cards, letterheads, and cohesive corporate suites.
              </p>
              <div class="hero-buttons">
                <a href="contact.php" class="btn btn-primary">Start Branding</a>
                <a href="#products" class="btn btn-secondary">Our Products</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide">
          <div class="hero-slide-bg print-bg-3" style="background: url('assets/print-slide-3.png') no-repeat center center; background-size: cover;"></div>
          <div class="hero-slide-overlay"></div>
          <div class="container hero-container">
            <div class="hero-content">
              <h1 class="hero-title">
                Vibrant <span>Merchandise</span>
              </h1>
              <p class="hero-subtitle">
                Custom branded apparel, hoodies, and promotional gifts designed to build lasting brand loyalty.
              </p>
              <div class="hero-buttons">
                <a href="contact.php" class="btn btn-primary">Order Merchandise</a>
                <a href="#products" class="btn btn-secondary">Browse Options</a>
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

    <link rel="stylesheet" href="./printing-modern.css">

    <section class="service-detail-section" id="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title" data-aos="fade-up">Premium Printing & Branding Catalog</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Explore our high-quality branding solutions tailored for your business success.</p>
            </div>
            <!-- Dynamic Catalog Container -->
            <div id="dynamicCatalog">
                <div class="catalog-loading">
                    <div class="loader"></div>
                    <p>Loading premium catalog...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Modal -->
    <div class="modal" id="orderModal">
        <div class="modal-container">
            <button class="modal-close" id="closeModal" aria-label="Close Order Modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            
            <div class="modal-product-layout">
                <div class="modal-product-image-side">
                    <img src="" alt="" id="modalImage">
                </div>
                <div class="modal-product-content-side">
                    <h2 class="modal-product-title" id="modalTitle">Product Name</h2>
                    <div class="modal-product-price" id="modalPrice">KES 0</div>
                    <p class="modal-product-description" id="modalDescription">Detailed product description goes here...</p>
                    
                    <div class="quantity-controller">
                        <span class="quantity-label">Quantity:</span>
                        <div class="quantity-selector">
                            <button class="qty-btn" id="qtyMinus" aria-label="Decrease quantity">-</button>
                            <input type="number" class="qty-input" value="1" min="1" id="qtyInput" aria-label="Order quantity">
                            <button class="qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="btn btn-primary btn-place-order" id="placeOrderBtn">Place Order</button>
                    </div>
                </div>
            </div>

            <!-- Success Overlay -->
            <div class="order-success-overlay" id="successOverlay">
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h2>Order Placed Successfully!</h2>
                <p>Thank you for choosing Shanfix Technology. Our team will contact you shortly to finalize your order details.</p>
                <button class="btn btn-primary btn-continue-shopping" id="continueShopping">Continue Shopping</button>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
