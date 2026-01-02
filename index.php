<?php
$page_title = 'Elite Salon - Premium Hair & Beauty Services';
require_once 'includes/auth.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Hero Section with Glassmorphism -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-glass-card text-center fade-in-up">
                    <h1 class="fade-in">Welcome to Elite Salon</h1>
                    <p class="hero-subtitle fade-in">Experience Premium Hair & Beauty Services</p>
                    <p class="lead fade-in">Transform your look with our professional stylists and state-of-the-art facilities. Discover the luxury you deserve with personalized beauty treatments designed just for you.</p>
                    <div class="fade-in mt-4">
                        <?php if (is_logged_in()): ?>
                            <a href="<?php echo get_dashboard_url(get_user_role()); ?>" class="btn btn-light btn-lg">Go to Dashboard</a>
                        <?php else: ?>
                            <a href="/elite-salon/register.php" class="btn btn-light btn-lg">Get Started</a>
                            <a href="/elite-salon/login.php" class="btn btn-outline-light btn-lg">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section with Glass Panel -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=800&h=600&fit=crop" alt="Elite Salon Interior" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-glass-panel">
                    <h2>About Elite Salon</h2>
                    <p class="lead">Your Premier Destination for Beauty & Style</p>
                    <p>At Elite Salon, we believe that everyone deserves to look and feel their best. With over 10 years of experience in the beauty industry, our team of expert stylists and beauticians are dedicated to providing exceptional services tailored to your unique style and preferences.</p>
                    <p>We use only the finest products and latest techniques to ensure you receive the best possible results. Whether you're looking for a fresh new haircut, vibrant color, or a complete makeover, we're here to make your beauty dreams a reality.</p>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="about-feature">
                                    <h4><i class="bi bi-check-circle-fill"></i> Expert Stylists</h4>
                                    <p>Highly trained professionals with years of experience</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="about-feature">
                                    <h4><i class="bi bi-check-circle-fill"></i> Premium Products</h4>
                                    <p>Only the best quality products for your hair and skin</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="about-feature">
                                    <h4><i class="bi bi-check-circle-fill"></i> Modern Facilities</h4>
                                    <p>State-of-the-art equipment and comfortable environment</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="about-feature">
                                    <h4><i class="bi bi-check-circle-fill"></i> Affordable Prices</h4>
                                    <p>Competitive pricing without compromising on quality</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section with Professional Images -->
<section id="services" class="services-section">
    <div class="container">
        <h2>Our Premium Services</h2>
        <p class="section-subtitle">Discover our comprehensive range of beauty and styling services</p>
        
        <div class="row">
            <!-- Haircut Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://png.pngtree.com/thumb_back/fh260/background/20220609/pngtree-bearded-hipster-confidently-styled-by-skilled-barber-in-barbershop-and-mens-beauty-salon-advertisement-photo-image_45814958.jpg" alt="Haircut Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-scissors"></i>
                        <h4>Haircut</h4>
                        <p>Professional haircuts for men and women with expert styling and personalized consultation</p>
                    </div>
                </div>
            </div>

            <!-- Hair Coloring Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=300&fit=crop" alt="Hair Coloring Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-palette-fill"></i>
                        <h4>Hair Coloring</h4>
                        <p>Vibrant colors and professional highlights by expert colorists using premium products</p>
                    </div>
                </div>
            </div>

            <!-- Hair Treatment Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=400&h=300&fit=crop" alt="Hair Treatment Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-star-fill"></i>
                        <h4>Hair Treatment</h4>
                        <p>Deep conditioning and repair treatments for healthy, shiny, and manageable hair</p>
                    </div>
                </div>
            </div>

            <!-- Styling Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1560869713-7d0a29430803?w=400&h=300&fit=crop" alt="Styling Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-brush-fill"></i>
                        <h4>Styling</h4>
                        <p>Special occasion styling for weddings, parties, and professional events</p>
                    </div>
                </div>
            </div>

            <!-- Beard Grooming Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://barberian.com/cdn/shop/files/Barberian_-_Beard_Trim.jpg?v=1747350781&width=1500" alt="Beard Grooming Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-person-fill"></i>
                        <h4>Beard Grooming</h4>
                        <p>Professional beard trimming, shaping, and grooming services for the modern gentleman</p>
                    </div>
                </div>
            </div>

            <!-- Manicure Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&h=300&fit=crop" alt="Manicure Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-hand-index-thumb-fill"></i>
                        <h4>Manicure</h4>
                        <p>Complete nail care with beautiful polish application and hand massage</p>
                    </div>
                </div>
            </div>

            <!-- Pedicure Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1610992015732-2449b76344bc?w=400&h=300&fit=crop" alt="Pedicure Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-heart-fill"></i>
                        <h4>Pedicure</h4>
                        <p>Relaxing foot care with professional nail polish and soothing foot treatment</p>
                    </div>
                </div>
            </div>

            <!-- Spa Treatments Service -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card">
                    <img src="https://www.viviyancosmetics.com/wp-content/uploads/2023/03/male-facial-scaled-1.jpg" alt="Spa Treatments Service" class="service-card-img">
                    <div class="service-card-body">
                        <i class="bi bi-droplet-half"></i>   <!-- Skin / hydration -->

                        <h4>Men’s Body & Skin Care</h4>
                        <p>Advanced treatments improving skin health, relaxation, and overall well-being.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA / Booking Section with Glass Panel -->
<section class="cta-section">
    <div class="container">
        <div class="cta-glass-panel">
            <h2>Ready to Transform Your Look?</h2>
            <p>Book your appointment today and experience the Elite Salon difference. Our expert team is waiting to help you look and feel your absolute best.</p>
            <?php if (!is_logged_in()): ?>
                <a href="/elite-salon/register.php" class="btn btn-primary btn-lg">Book an Appointment Now</a>
            <?php else: ?>
                <a href="/elite-salon/user/appointments.php" class="btn btn-primary btn-lg">Book an Appointment Now</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">
        <h2>Get In Touch</h2>
        <p class="section-subtitle">We'd love to hear from you. Visit us or contact us today!</p>
        
        <div class="row mt-5">
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <i class="bi bi-geo-alt-fill"></i>
                    <h4>Visit Us</h4>
                    <p>123 Beauty Street<br>Style City, ST 12345<br>United States</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <i class="bi bi-telephone-fill"></i>
                    <h4>Call Us</h4>
                    <p>+1 (555) 0100<br>Mon-Sat: 9:00 AM - 8:00 PM<br>Sunday: Closed</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <i class="bi bi-envelope-fill"></i>
                    <h4>Email Us</h4>
                    <p>info@elitesalon.com<br>support@elitesalon.com<br>We reply within 24 hours</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Footer -->
<footer>
    <div class="container">
        <div class="row">
            <!-- About Elite Salon Column -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="bi bi-scissors"></i> Elite Salon
                    </div>
                    <p class="footer-description">
                        Your premier destination for luxury hair and beauty services. We combine expertise, premium products, and exceptional service to deliver outstanding results every time.
                    </p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/" class="social-link" aria-label="Facebook" target="_blank">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.instagram.com/" class="social-link" aria-label="Instagram" target="_blank">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://twitter.com/" class="social-link" aria-label="Twitter" target="_blank">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.tiktok.com/" class="social-link" aria-label="TikTok" target="_blank">
                                <i class="bi bi-tiktok"></i>
                            </a>
                        </div>

                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="/elite-salon/index.php">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <?php if (!is_logged_in()): ?>
                            <li><a href="/elite-salon/login.php">Login</a></li>
                            <li><a href="/elite-salon/register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Services Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Our Services</h5>
                    <ul class="footer-links">
                        <li><a href="#services">Haircut & Styling</a></li>
                        <li><a href="#services">Hair Coloring</a></li>
                        <li><a href="#services">Hair Treatment</a></li>
                        <li><a href="#services">Beard Grooming</a></li>
                        <li><a href="#services">Manicure & Pedicure</a></li>
                        <li><a href="#services">Spa Treatments</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-section">
                    <h5>Contact Info</h5>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            123 Beauty Street<br>
                            Style City, ST 12345
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <div>+1 (555) 0100</div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>info@elitesalon.com</div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock-fill"></i>
                        <div>Mon-Sat: 9AM - 8PM</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row">
                <div class="col-md-12">
                    <p>&copy; 2026 Elite Salon. All rights reserved. | <span class="powered-by">Powered by Elite Salon</span></p>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php require_once 'includes/footer.php'; ?>
