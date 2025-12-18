<?php
require_once 'includes/config.php';  // This starts the session

// If user is already logged in, send them to their dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<?php
/**
 * FindMyDojo - Home Page
 * Main landing page with hero, featured dojos, styles, features, and testimonials
 */

// Page-specific variables
$page_title = 'Home';
$page_description = 'Find the best martial arts dojos near you. Explore karate, jiu-jitsu, muay thai, taekwondo schools. Connect with masters and join a global martial arts community.';

// Include header
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section"></section>

<!-- Hero Section: Text Left + Slideshow Right -->
<section class="hero-section" style="padding: 6rem 0 8rem; background: var(--color-background); margin-top: -10rem; ">
    <div class="container">
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
            <!-- Left: Text & Buttons -->
            <div data-aos="fade-right">
                <h1 class="section-title" style="font-size: 3.5rem; margin-bottom: 1.25rem; color: var(--color-secondary); line-height: 1.2;">
                    DISCOVER YOUR PERFECT<br>
                    <span style="color: var(--color-primary);">MARTIAL ARTS DOJO</span>
                </h1>
                <p class="section-description" style="font-size: 1.125rem; margin-bottom: 2.5rem; max-width: 480px; line-height: 1.7;">
                    Find, connect, and train with the best martial arts communities near you. 
                    Explore dojos, styles, and events tailored to your journey.
                </p>
                <div class="hero-actions" style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                    <a href="dojos.php" class="btn btn-primary btn-lg" style="padding: 0.875rem 2rem; border-radius: 50px; font-size: 1.125rem;">
                        Find Dojos
                    </a>
                    <a href="help.php" class="btn btn-outline btn-lg" style="padding: 0.875rem 2rem; border-radius: 50px; font-size: 1.125rem; border: 3px solid var(--color-primary); color: var(--color-primary);">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Right: Slideshow (Reduced Size) -->
            <div class="hero-slideshow-container" style="position: relative; height: 400px; border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-xl);" data-aos="fade-left">
                <?php 
                $slides = [
                    "https://images.unsplash.com/photo-1555597673-b21d5c935865?w=600&h=400&fit=crop",
                    'assets/images/JUDO.jpg',
                    'assets/images/img1.jpg',
                    'assets/images/img3.jpg',
                    'assets/images/img4.jpg',
                    'assets/images/img5.jpg',
                    'assets/images/img6.jpg',
                    'assets/images/img7.jpg',
                    'assets/images/img8.jpg',
                    'assets/images/wushu.jpg',
                    'assets/images/Kendo.jpg',
                    'assets/images/Wrestling.jpg'
                ];
                foreach ($slides as $index => $slide): ?>
                <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="position: absolute; inset: 0; background-image: url('<?php echo $slide; ?>'); 
                            background-size: cover; background-position: center; 
                            opacity: <?php echo $index === 0 ? '1' : '0'; ?>; 
                            transition: opacity 1.5s ease;">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Slideshow JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    let current = 0;

    function nextSlide() {
        slides[current].style.opacity = '0';
        current = (current + 1) % slides.length;
        slides[current].style.opacity = '1';
    }

    setInterval(nextSlide, 6000);

    // Preload images
    slides.forEach(slide => {
        const img = new Image();
        img.src = slide.style.backgroundImage.slice(5, -2);
    });
});
</script>

<style>
.hero-slide { transition: opacity 1.5s ease-in-out; }

@media (max-width: 1024px) {
    .grid { grid-template-columns: 1fr !important; text-align: center; }
    .hero-actions { justify-content: center; }
    .hero-slideshow-container { height: 350px; margin-top: 2.5rem; }
}

@media (max-width: 768px) {
    .section-title { font-size: 2.8rem; }
    .hero-slideshow-container { height: 280px; }
}
</style>







<!-- Featured Dojos Section -->
<section class="section" style="background: linear-gradient(to bottom, hsl(0, 0%, 90%, 0.5), hsl(0, 0%, 90%, 0.8));">
    <div class="container">
        <div data-aos="fade-up">
            <h2 class="section-title">Featured Dojos</h2>
        </div>
        
        <div class="grid grid-cols-3" data-stagger-cards>
            <!-- Dojo Card 1 -->
            <div class="dojo-card hover-lift" data-aos="fade-up" data-aos-delay="0">
                <div class="dojo-image">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&h=400&fit=crop" alt="Dragon's Den Dojo">
                    <div class="dojo-image-overlay"></div>
                    <div class="dojo-badge">Since 1998</div>
                    <div class="dojo-rating">
                        <i class="fas fa-star" style="color: var(--color-primary);"></i> 5.0
                    </div>
                </div>
                <div class="dojo-content">
                    <div class="dojo-style">Kung Fu • Wushu</div>
                    <h3 class="dojo-name">Dragon's Den Dojo</h3>
                    <div class="dojo-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>London, UK</span>
                    </div>
                    <p style="color: var(--color-muted-foreground); font-size: 0.875rem; margin-bottom: 1rem;">
                        Traditional Kung Fu & Wushu training in a serene environment with master instructors.
                    </p>
                    <div class="dojo-footer">
                        <span><i class="fas fa-users"></i> 450+ members</span>
                        <span>234 reviews</span>
                    </div>
                </div>
            </div>
            
            <!-- Dojo Card 2 -->
            <div class="dojo-card hover-lift" data-aos="fade-up" data-aos-delay="100">
                <div class="dojo-image">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=400&fit=crop" alt="Iron Fist Academy">
                    <div class="dojo-image-overlay"></div>
                    <div class="dojo-badge">Since 2005</div>
                    <div class="dojo-rating">
                        <i class="fas fa-star" style="color: var(--color-primary);"></i> 4.8
                    </div>
                </div>
                <div class="dojo-content">
                    <div class="dojo-style">MMA • BJJ</div>
                    <h3 class="dojo-name">Iron Fist Academy</h3>
                    <div class="dojo-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>New York, USA</span>
                    </div>
                    <p style="color: var(--color-muted-foreground); font-size: 0.875rem; margin-bottom: 1rem;">
                        Modern MMA and Brazilian Jiu-Jitsu with world-class instructors and competitive programs.
                    </p>
                    <div class="dojo-footer">
                        <span><i class="fas fa-users"></i> 320+ members</span>
                        <span>189 reviews</span>
                    </div>
                </div>
            </div>
            
            <!-- Dojo Card 3 -->
            <div class="dojo-card hover-lift" data-aos="fade-up" data-aos-delay="200">
                <div class="dojo-image">
                    <img src="https://images.unsplash.com/photo-1555597673-b21d5c935865?w=600&h=400&fit=crop" alt="Sakura Karate Club">
                    <div class="dojo-image-overlay"></div>
                    <div class="dojo-badge">Since 1972</div>
                    <div class="dojo-rating">
                        <i class="fas fa-star" style="color: var(--color-primary);"></i> 5.0
                    </div>
                </div>
                <div class="dojo-content">
                    <div class="dojo-style">Shotokan • Kobudo</div>
                    <h3 class="dojo-name">Sakura Karate Club</h3>
                    <div class="dojo-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Tokyo, Japan</span>
                    </div>
                    <p style="color: var(--color-muted-foreground); font-size: 0.875rem; margin-bottom: 1rem;">
                        Authentic Japanese Karate with a focus on discipline, tradition, and character development.
                    </p>
                    <div class="dojo-footer">
                        <span><i class="fas fa-users"></i> 670+ members</span>
                        <span>421 reviews</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center" style="margin-top: 3rem;" data-aos="fade-up" data-aos-delay="300">
            <a href="dojos.php" class="btn btn-outline btn-lg">View All Dojos</a>
        </div>
    </div>
</section>

<!-- Martial Arts Styles Section -->
<section class="section" style="background-color: var(--color-muted);">
    <div class="container">
        <div data-aos="fade-up">
            <h2 class="section-title">Martial Arts Styles</h2>
        </div>
        
        <div class="grid grid-cols-4">
            <?php
            $styles = [
                ['name' => 'Karate', 'description' => 'Japanese striking art focusing on punches, kicks, and blocks', 'image' => 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=400&h=300&fit=crop'],
                ['name' => 'Judo', 'description' => 'Japanese grappling art emphasizing throws and groundwork', 'image' => 'https://images.unsplash.com/photo-1564415315949-7a0c4c73aab4?w=400&h=300&fit=crop'],
                ['name' => 'Tai Chi', 'description' => 'Chinese internal martial art known for slow, flowing movements', 'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&h=300&fit=crop'],
                ['name' => 'Brazilian Jiu-Jitsu', 'description' => 'Ground-focused grappling art emphasizing submissions', 'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop'],
            ];
            
            foreach ($styles as $index => $style):
            ?>
            <a href="dojos.php?style=<?php echo urlencode(strtolower($style['name'])); ?>" 
               class="hover-lift" 
               data-aos="fade-up" 
               data-aos-delay="<?php echo $index * 100; ?>">
                <div class="glass-card" style="padding: 0; overflow: hidden;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="<?php echo $style['image']; ?>" 
                             alt="<?php echo $style['name']; ?>"
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div style="padding: 1.5rem; text-align: center;">
                        <h3 style="font-size: 1.25rem; color: var(--color-secondary); margin-bottom: 0.5rem;">
                            <?php echo $style['name']; ?>
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--color-muted-foreground);">
                            <?php echo $style['description']; ?>
                        </p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section" style="background-color: var(--color-card);">
    <div class="container">
        <div data-aos="fade-up">
            <h2 class="section-title">Why Choose FindMyDojo?</h2>
        </div>
        
        <div class="grid grid-cols-3">
            <!-- Feature 1 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="feature-title">Smart Discovery</h3>
                <p class="feature-description">
                    Find dojos by location, style, and experience level. Our smart filters help you discover the perfect match for your martial arts journey.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <h3 class="feature-title">Events & Workshops</h3>
                <p class="feature-description">
                    Discover local tournaments, seminars, and special training events in your community. Never miss an opportunity to grow.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">Verified Reviews</h3>
                <p class="feature-description">
                    Read authentic reviews from fellow martial artists to make informed decisions about where to train.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section" style="position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, hsl(77, 44%, 25%, 0.05), transparent);"></div>
    
    <div class="container" style="position: relative; z-index: 10;">
        <div data-aos="fade-up">
            <h2 class="section-title">
                <span style="color: var(--color-foreground);">WHAT</span>
                <span class="gradient-text">WARRIORS SAY</span>
            </h2>
            <p class="section-description">
                Join thousands of martial artists who have found their perfect training ground
            </p>
        </div>
        
        <div class="grid grid-cols-3">
            <?php
            $testimonials = [
                [
                    'name' => 'Marcus Johnson',
                    'role' => 'BJJ Practitioner',
                    'location' => 'New York, USA',
                    'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
                    'rating' => 5,
                    'text' => 'FindMyDojo helped me discover an amazing BJJ academy just 10 minutes from my home. The detailed profiles and genuine reviews made my decision so much easier.'
                ],
                [
                    'name' => 'Yuki Tanaka',
                    'role' => 'Karate Sensei',
                    'location' => 'Osaka, Japan',
                    'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop&crop=face',
                    'rating' => 5,
                    'text' => 'As a dojo owner, this platform has brought us students from all over the world. The exposure and community features are invaluable for growing our school.'
                ],
                [
                    'name' => 'Alex Rivera',
                    'role' => 'MMA Fighter',
                    'location' => 'Miami, USA',
                    'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face',
                    'rating' => 5,
                    'text' => 'I travel frequently for competitions and FindMyDojo makes it easy to find quality training wherever I go. It\'s become essential for my fight camps.'
                ]
            ];
            
            foreach ($testimonials as $index => $testimonial):
            ?>
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                <div class="quote-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <div class="testimonial-rating">
                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text">
                    "<?php echo $testimonial['text']; ?>"
                </p>
                <div class="testimonial-author">
                    <img src="<?php echo $testimonial['image']; ?>" 
                         alt="<?php echo $testimonial['name']; ?>" 
                         class="author-image">
                    <div class="author-info">
                        <div class="author-name"><?php echo $testimonial['name']; ?></div>
                        <div class="author-role">
                            <?php echo $testimonial['role']; ?> • <?php echo $testimonial['location']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: var(--color-secondary); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0;">
        <div style="position: absolute; top: 30%; left: 20%; width: 300px; height: 300px; background: hsla(24, 72%, 50%, 0.1); border-radius: 50%; filter: blur(100px);"></div>
        <div style="position: absolute; bottom: 20%; right: 30%; width: 200px; height: 200px; background: hsla(0, 0%, 100%, 0.05); border-radius: 50%; filter: blur(80px);"></div>
    </div>
    
    <div class="container text-center" style="position: relative; z-index: 10;">
        <h2 data-aos="fade-up" style="font-size: 2.5rem; color: white; margin-bottom: 1.5rem;">
            Ready to Begin Your Journey?
        </h2>
        <p data-aos="fade-up" data-aos-delay="100" style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.8); max-width: 700px; margin: 0 auto 2.5rem;">
            Join thousands of martial artists discovering new dojos and styles every day. 
            Your perfect training environment awaits.
        </p>
        <div data-aos="fade-up" data-aos-delay="200" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
            <a href="dojos.php" class="btn btn-lg" style="background: var(--color-card); color: var(--color-secondary);">
                Find Dojos
            </a>
            <a href="auth.php?mode=register" class="btn btn-primary btn-lg">
                Become a Dojo Owner
            </a>
        </div>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>