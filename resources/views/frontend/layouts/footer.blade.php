<footer class="site-footer">
    <!-- Floating Glassmorphic Newsletter Bar -->
    <div class="site-footer-top py-4">
        <div class="container">
            <div class="footer-newsletter-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 col-md-12">
                        <div class="footer-brand-wrap d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            <a href="{{ url('/') }}" class="footer-logo-link">
                                <img src="{{ url('frontend/360logo.webp') }}" alt="360 PropGuide" class="footer-logo-img" width="180" height="70" loading="lazy" decoding="async">
                            </a>
                            <div class="footer-quick-contacts d-flex flex-wrap gap-2">
                                <a href="mailto:info@360propguide.com" class="footer-contact-chip">
                                    <i class="fa-solid fa-envelope text-coral"></i>
                                    <span>info@360propguide.com</span>
                                </a>
                                <a href="tel:+919643020020" class="footer-contact-chip">
                                    <i class="fa-solid fa-phone text-coral"></i>
                                    <span>+91 9643-020-020</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <form action="{{ route('subscribe') }}" method="post" class="footer-newsletter-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <p class="newsletter-title mb-2">
                                <i class="fa-solid fa-bell text-coral me-1"></i> Stay Ahead of NCR Property Trends
                            </p>
                            <div class="input-group newsletter-input-group">
                                <input type="email" name="email" class="form-control newsletter-input" placeholder="Enter Your Email Address" required>
                                <button type="submit" class="btn btn-theme-primary newsletter-btn">
                                    <span>Subscribe2</span>
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Body -->
    <div class="site-footer-body py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Col 1: About -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="footer-widget widget-about">
                        <h4 class="footer-widget-title">About 360 PropGuide</h4>
                        <p class="footer-bio-text">
                            <strong>360 PropGuide</strong> redefines property buying in Delhi NCR with a transparent, advisory-first approach. From property selection and site visits to home loans, legal due diligence, and turnkey interiors, our expert advisors ensure your home buying journey is seamless and rewarding.
                        </p>
                        <div class="footer-social-wrap mt-4">
                            <h5 class="social-heading">Connect With Us</h5>
                            <div class="footer-social-icons">
                                <a href="https://www.facebook.com/360propguide/" target="_blank" rel="noopener noreferrer" class="social-icon-btn facebook" title="Facebook">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="https://www.instagram.com/360propguideofficial/" target="_blank" rel="noopener noreferrer" class="social-icon-btn instagram" title="Instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                                <a href="https://www.linkedin.com/company/360propguide/" target="_blank" rel="noopener noreferrer" class="social-icon-btn linkedin" title="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                                <a href="https://wa.me/+919643020020" target="_blank" rel="noopener noreferrer" class="social-icon-btn whatsapp" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                                <a href="https://www.youtube.com/@360_Propguide" target="_blank" rel="noopener noreferrer" class="social-icon-btn youtube" title="YouTube">
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Top Projects -->
                <div class="col-lg-2 col-md-6 col-6">
                    <div class="footer-widget widget-links">
                        <h4 class="footer-widget-title">Top Projects</h4>
                        <ul class="footer-menu-list">
                            <li><a href="{{ url('/projects/renox-thrive') }}"><i class="fa-solid fa-chevron-right"></i> Renox Thrive</a></li>
                            <li><a href="{{ url('/projects/ivy-county') }}"><i class="fa-solid fa-chevron-right"></i> Ivy County</a></li>
                            <li><a href="{{ url('/projects/amrapali-golf-homes') }}"><i class="fa-solid fa-chevron-right"></i> Amrapali Golf Homes</a></li>
                            <li><a href="{{ url('/projects/amrapali-enchante') }}"><i class="fa-solid fa-chevron-right"></i> Amrapali Enchante</a></li>
                            <li><a href="{{ url('/projects/elite-x') }}"><i class="fa-solid fa-chevron-right"></i> Elite X</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Col 3: Quick Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <div class="footer-widget widget-links">
                        <h4 class="footer-widget-title">Important Links</h4>
                        <ul class="footer-menu-list">
                            <li><a href="{{ url('about-us') }}"><i class="fa-solid fa-chevron-right"></i> Our Passion</a></li>
                            <li><a href="{{ url('projects') }}"><i class="fa-solid fa-chevron-right"></i> Explore Projects</a></li>
                            <li><a href="{{ url('blogs') }}"><i class="fa-solid fa-chevron-right"></i> Knowledge Base</a></li>
                            <li><a href="{{ url('careers') }}"><i class="fa-solid fa-chevron-right"></i> Join Our Journey</a></li>
                            <li><a href="{{ url('contact') }}"><i class="fa-solid fa-chevron-right"></i> Contact Us</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Col 4: Offices & Contact -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="footer-widget widget-contact">
                        <h4 class="footer-widget-title">Our Offices</h4>
                        <div class="office-location-list">
                            <!-- Noida Office -->
                            <div class="office-location-card mb-3">
                                <div class="office-icon-wrap"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <h6 class="office-city">Noida Corporate Office</h6>
                                    <a href="https://g.co/kgs/UtKoFqn" target="_blank" rel="noopener noreferrer" class="office-address-link">
                                        4th Floor, Chandra Heights, Sector 107, Noida, Uttar Pradesh
                                    </a>
                                </div>
                            </div>

                            <!-- Ghaziabad Office -->
                            <div class="office-location-card mb-3">
                                <div class="office-icon-wrap"><i class="fa-solid fa-building"></i></div>
                                <div>
                                    <h6 class="office-city">Ghaziabad Branch Office</h6>
                                    <a href="https://g.co/kgs/UtKoFqn" target="_blank" rel="noopener noreferrer" class="office-address-link">
                                        2nd Floor, Plot 8K/14 (Adjoining DPS School), Siddharth Vihar, Ghaziabad
                                    </a>
                                </div>
                            </div>

                            <!-- Office Hours -->
                            <div class="office-location-card">
                                <div class="office-icon-wrap"><i class="fa-regular fa-clock"></i></div>
                                <div>
                                    <h6 class="office-city">Working Hours</h6>
                                    <p class="office-hours-text mb-0">Mon – Sat: 10:00 AM – 07:30 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright Bar -->
    <div class="site-footer-bottom py-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">
                <p class="copyright-text mb-0">
                    &copy; 2026 <strong>360 PropGuide LLP</strong>. All Rights Reserved. Designed &amp; Developed with <i class="fa-solid fa-heart text-coral"></i> by AdvertIQ
                </p>
                <div class="footer-legal-links d-flex gap-3">
                    <a href="{{ url('privacy') }}">Privacy Policy</a>
                    <span class="legal-divider">|</span>
                    <a href="{{ url('disclaimer') }}">Disclaimer</a>
                    <span class="legal-divider">|</span>
                    <a href="{{ url('terms') }}">Terms Of Use</a>
                </div>
            </div>
        </div>
    </div>
</footer>
