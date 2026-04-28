<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="portfolio-modern.css">

<main class="page-modern-portfolio">
    <!-- Hero Section -->
    <section class="portfolio-hero">
        <div class="portfolio-hero-content">
            <h1 class="portfolio-hero-title" data-aos="fade-up">Our <span class="highlight">Portfolio</span></h1>
            <p class="portfolio-hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                Discover how we engineer digital excellence through bold designs, robust architecture, and flawless execution for forward-thinking brands worldwide.
            </p>
        </div>
    </section>

    <!-- Featured Case Studies -->
    <section class="featured-work-section container">
        <div class="section-label" data-aos="fade-right">Featured Case Studies</div>
        
        <!-- Case Study 1 -->
        <article class="featured-project" data-aos="fade-up">
            <div class="fp-image">
                <img src="assets/mockup_ecommerce.png" alt="Skyline E-Commerce Platform Mockup">
            </div>
            <div class="fp-content">
                <span class="fp-badge">E-Commerce & Retail</span>
                <h2 class="fp-title">Skyline E-Commerce Platform</h2>
                <p class="fp-desc">
                    We architected a high-conversion, headless e-commerce solution that seamlessly handles high traffic volumes while providing an immersive, blazing-fast shopping experience. Integrated native AI recommendations to increase basket sizes.
                </p>
                <div class="fp-stats-grid">
                    <div class="fp-stat">
                        <div class="fp-stat-val">+140%</div>
                        <div class="fp-stat-label">Sales Conversion</div>
                    </div>
                    <div class="fp-stat">
                        <div class="fp-stat-val">< 0.8s</div>
                        <div class="fp-stat-label">Page Load Time</div>
                    </div>
                </div>
                <a href="contact.php" class="btn btn-primary">Start Similar Project</a>
            </div>
        </article>

        <!-- Case Study 2 -->
        <article class="featured-project reverse" data-aos="fade-up">
            <div class="fp-image">
                <img src="assets/mockup_fintech.png" alt="Apex Financial Dashboard Mockup">
            </div>
            <div class="fp-content">
                <span class="fp-badge">Fintech & Analytics</span>
                <h2 class="fp-title">Apex Real-time Trading Dashboard</h2>
                <p class="fp-desc">
                    A mission-critical financial analytics platform delivering real-time trading data with zero latency. 
                    We designed a highly customizable, dark-themed UI that reduces cognitive load for traders analyzing complex data streams.
                </p>
                <div class="fp-stats-grid">
                    <div class="fp-stat">
                        <div class="fp-stat-val">10ms</div>
                        <div class="fp-stat-label">Data Latency</div>
                    </div>
                    <div class="fp-stat">
                        <div class="fp-stat-val">100k+</div>
                        <div class="fp-stat-label">Concurrent Users</div>
                    </div>
                </div>
                <a href="contact.php" class="btn btn-primary">Start Similar Project</a>
            </div>
        </article>

        <!-- Case Study 3 -->
        <article class="featured-project" data-aos="fade-up">
            <div class="fp-image">
                <img src="assets/mockup_app.png" alt="Nexus Healthcare Application Mockup">
            </div>
            <div class="fp-content">
                <span class="fp-badge">Mobile App Development</span>
                <h2 class="fp-title">Nexus Health tracking App</h2>
                <p class="fp-desc">
                    A vibrant, highly intuitive cross-platform mobile application designed to gamify personal health and wellness. 
                    Includes secure biometric login, real-time IoT wearable synchronization, and personalized daily goal tracking.
                </p>
                <div class="fp-stats-grid">
                    <div class="fp-stat">
                        <div class="fp-stat-val">4.9★</div>
                        <div class="fp-stat-label">App Store Rating</div>
                    </div>
                    <div class="fp-stat">
                        <div class="fp-stat-val">1M+</div>
                        <div class="fp-stat-label">Active Downloads</div>
                    </div>
                </div>
                <a href="contact.php" class="btn btn-primary">Start Similar Project</a>
            </div>
        </article>
    </section>

    <!-- Testimonial Ribbon -->
    <section class="testimonial-ribbon">
        <div class="container tr-content" data-aos="zoom-in">
            <div class="tr-stars">
                <!-- 5 Stars SVG -->
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
            </div>
            <blockquote class="tr-quote">
                "Shanfix didn't just build our platform; they completely revolutionized our digital business model. Their premium aesthetic and engineering depth are unmatched."
            </blockquote>
            <p class="tr-author">Sarah Jenkins</p>
            <p class="tr-company">CTO, Global Retail Enterprises</p>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
