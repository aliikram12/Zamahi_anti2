    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="site-footer" id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <div class="footer-logo">
                            <span class="logo-text">ZAMAHI</span>
                            <span class="logo-sub">LUXURY CATERING</span>
                        </div>
                        <p class="footer-tagline">Luxury Catering. Exceptional Events.</p>
                        <p class="footer-legal">Operated by <?= LEGAL_ENTITY ?></p>
                    </div>
                    <div class="footer-col">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#events">Our Events</a></li>
                            <li><a href="#services">Services</a></li>
                            <li><a href="#menu-booking">Menu & Booking</a></li>
                            <li><a href="#gallery">Gallery</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Contact</h4>
                        <ul>
                            <li><i class="fas fa-phone"></i> <?= SITE_PHONE ?></li>
                            <li><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></li>
                            <li><i class="fas fa-map-marker-alt"></i> <?= SITE_ADDRESS ?></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Follow Us</h4>
                        <div class="social-links">
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                            <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. | Operated by <?= LEGAL_ENTITY ?></p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/442071234567?text=Hi%20ZAMAHI!%20I'd%20like%20to%20enquire%20about%20catering."
       class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
