</main>
    <!-- End Main Content -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <a href="index.php" class="footer-logo">
                        <span class="logo-icon">🐉</span>
                        <span class="logo-text"><?php echo SITE_NAME; ?></span>
                    </a>
                    <p class="footer-description">
                        Empowering martial arts communities worldwide through technology and connection.
                    </p>
                    <div class="social-links">
                        <?php foreach ($social_links as $social): ?>
                            <a href="<?php echo $social['url']; ?>" 
                               class="social-link" 
                               aria-label="<?php echo $social['platform']; ?>"
                               target="_blank" 
                               rel="noopener noreferrer">
                                <i class="fab fa-<?php echo $social['icon']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Platform Links -->
                <div class="footer-section">
                    <h4 class="footer-title">Platform</h4>
                    <ul class="footer-links">
                        <?php foreach ($footer_links['platform'] as $link): ?>
                            <li>
                                <a href="<?php echo $link['path']; ?>"><?php echo $link['name']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Company Links -->
                <div class="footer-section">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <?php foreach ($footer_links['company'] as $link): ?>
                            <li>
                                <a href="<?php echo $link['path']; ?>"><?php echo $link['name']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?php echo JS_PATH; ?>main.js"></script>
    <script src="<?php echo JS_PATH; ?>animations.js"></script>
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo JS_PATH . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>