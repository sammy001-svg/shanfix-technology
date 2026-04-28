<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="contact-modern-v2.css">
<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<main class="page-modern-contact">
    <!-- Ultra-Premium Hero V2 -->
    <section class="contact-hero-v2">
        <div class="hero-v2-content">
            <div class="availability-badge" data-aos="fade-down">
                <span class="dot"></span>
                Currently accepting new projects
            </div>
            <h1 class="hero-v2-title" data-aos="zoom-out" data-aos-duration="1000">Let's build<br>the future.</h1>
        </div>
    </section>

    <!-- Master Contact Grid -->
    <div class="contact-master-container">
        <div class="contact-v2-grid">
            
            <!-- Side Panel: Info & Socials -->
            <aside class="contact-v2-info" data-aos="fade-right">
                <div class="v2-info-header">
                    <h2>Reach Out</h2>
                    <p>Have a question or a project in mind? We'd love to hear from you. Our team is ready to respond within 24 hours.</p>
                </div>

                <div class="v2-contact-list">
                    <div class="v2-contact-card">
                        <div class="v2-card-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="v2-card-text">
                            <span>Phone</span>
                            <p>+254 751 869 165</p>
                        </div>
                    </div>

                    <div class="v2-contact-card">
                        <div class="v2-card-icon"><i class="fas fa-envelope"></i></div>
                        <div class="v2-card-text">
                            <span>Email</span>
                            <p>info@shanfix.tech</p>
                        </div>
                    </div>

                    <div class="v2-contact-card">
                        <div class="v2-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="v2-card-text">
                            <span>Location</span>
                            <p>Nairobi, Kenya</p>
                        </div>
                    </div>
                </div>

                <div class="v2-social-block">
                    <p style="color: #64748b; font-weight: 700; margin-bottom: 15px;">SOCIAL CHANNELS</p>
                    <div style="display: flex; gap: 15px;">
                        <a href="#" style="color: #fff; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                        <a href="#" style="color: #fff; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #fff; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: #fff; font-size: 1.5rem;"><i class="fab fa-dribbble"></i></a>
                    </div>
                </div>
            </aside>

            <!-- Main Panel: The Form -->
            <section class="contact-v2-form-area" data-aos="fade-left">
                <h2 class="v2-form-title">Start a Conversation</h2>
                
                <form action="#" class="modern-v2-form" id="contactFormV2">
                    <div class="v2-input-box">
                        <input type="text" id="name" required>
                        <label>Your Full Name</label>
                    </div>

                    <div class="v2-input-box">
                        <input type="email" id="email" required>
                        <label>Email Address</label>
                    </div>

                    <div class="v2-input-box">
                        <input type="text" id="subject" required>
                        <label>Interested In (e.g. Web Dev, App Design)</label>
                    </div>

                    <div class="v2-input-box">
                        <textarea id="message" required></textarea>
                        <label>Tell us about your project</label>
                    </div>

                    <button type="submit" class="v2-submit-btn">
                        Submit Request
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </section>
        </div>

        <!-- Integrated Map Section -->
        <div class="modern-map-wrapper" data-aos="fade-up" style="margin-top: 50px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.75053424619!2d36.71183187496587!3d-1.321684398670868!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1b88e1000001%3A0x6b4f738f71295b28!2sTana%20House%2C%20Nairobi!5e0!3m2!1sen!2ske!4v1714313400000!5m2!1sen!2ske" width="100%" height="450" style="border:0; filter: invert(90%) hue-rotate(150deg) brightness(95%) contrast(90%);" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
