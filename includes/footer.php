    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="site-footer" id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <div class="footer-logo">
                            <div class="logo-plate">
                                <svg width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 2C15 2 12 5 12 8V14C12 18 16 20 20 20C24 20 28 18 28 14V8C28 5 25 2 20 2Z" fill="url(#plateGradient)" stroke="url(#plateBorder)" stroke-width="1.5"/>
                                    <path d="M12 12C12 16 16 18 20 18C24 18 28 16 28 12" fill="none" stroke="url(#plateRim)" stroke-width="0.8" opacity="0.6"/>
                                    <ellipse cx="20" cy="10" rx="8" ry="3" fill="url(#plateInner)" opacity="0.3"/>
                                    <defs>
                                        <linearGradient id="plateGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:1" />
                                            <stop offset="50%" style="stop-color:#F4E87C;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#B8960F;stop-opacity:1" />
                                        </linearGradient>
                                        <linearGradient id="plateBorder" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#B8960F;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#8B6914;stop-opacity:1" />
                                        </linearGradient>
                                        <linearGradient id="plateRim" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:0.8" />
                                            <stop offset="100%" style="stop-color:#B8960F;stop-opacity:0.6" />
                                        </linearGradient>
                                        <radialGradient id="plateInner" cx="50%" cy="50%" r="50%">
                                            <stop offset="0%" style="stop-color:#FFFFFF;stop-opacity:0.2" />
                                            <stop offset="100%" style="stop-color:#D4AF37;stop-opacity:0.1" />
                                        </radialGradient>
                                    </defs>
                                </svg>
                            </div>
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
