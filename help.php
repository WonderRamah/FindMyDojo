<?php
/**
 * FindMyDojo - Help Center
 * Clean, beautiful, and functional help page
 */

$page_title = 'Help Center';
$page_description = 'Find answers to common questions and get support for your martial arts journey.';

require_once 'includes/header.php';

// FAQs (your existing ones)
$faqs = [
    ['question' => 'How do I find a dojo near me?', 'answer' => 'Use our search feature on the homepage or the "Find Dojos" page. You can filter by location, martial arts style, and more. Enable location services for more accurate results.'],
    ['question' => 'How do I create an account?', 'answer' => 'Click "Get Started" or "Sign In" in the navigation bar. You can sign up with your email address or through social login options. Follow the prompts to complete your profile.'],
    ['question' => 'How can I list my dojo on FindMyDojo?', 'answer' => 'Register as a dojo owner by selecting "List Your Dojo" on our homepage. Complete the verification process, add your dojo details, photos, schedule, and pricing. Our team will review and approve your listing within 48 hours.'],
    ['question' => 'Is FindMyDojo free to use?', 'answer' => 'Yes! Searching for dojos and creating a student account is completely free. Dojo owners have access to a free basic listing, with premium features available through our Pro plans.'],
    ['question' => 'How do reviews work?', 'answer' => 'Only verified members who have trained at a dojo can leave reviews. Reviews include ratings for instruction quality, facilities, atmosphere, and value. Dojo owners can respond to reviews publicly.'],
    ['question' => 'Can I book trial classes through FindMyDojo?', 'answer' => 'Yes! Many dojos offer trial class bookings directly through our platform. Look for the "Book Trial" button on dojo profiles. Some may redirect you to their own booking system.'],
];

// Popular topics
$popular_topics = [
    ['icon' => 'fa-search', 'title' => 'Finding Dojos', 'link' => '#faq-1'],
    ['icon' => 'fa-user-plus', 'title' => 'Creating an Account', 'link' => '#faq-2'],
    ['icon' => 'fa-building', 'title' => 'Listing Your Dojo', 'link' => '#faq-3'],
    ['icon' => 'fa-star', 'title' => 'Writing Reviews', 'link' => '#faq-5'],
    ['icon' => 'fa-calendar-check', 'title' => 'Booking Trials', 'link' => '#faq-6'],
    ['icon' => 'fa-headset', 'title' => 'Contact Support', 'link' => 'contact.php'],
];
?>

<div style="padding-top: 8rem; padding-bottom: 6rem;">
    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin-bottom: 4rem;" data-aos="fade-up">
            <h1 class="section-title">
                <span style="color: var(--color-foreground);">HELP</span>
                <span class="gradient-text">CENTER</span>
            </h1>
            <p class="section-description" style="max-width: 700px; margin: 1.5rem auto 0;">
                Find quick answers or get personalized support for your martial arts journey.
            </p>

            <!-- Search Bar -->
            <div style="max-width: 600px; margin: 2.5rem auto 0;">
                <div class="glass-card" style="padding: 0.75rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-search" style="font-size: 1.5rem; color: var(--color-primary);"></i>
                    <input type="text" id="helpSearch" placeholder="Search questions, guides, or topics..." 
                           style="flex: 1; background: transparent; border: none; outline: none; font-size: 1.125rem; color: var(--color-foreground);">
                </div>
            </div>
        </div>

        <!-- Hero Illustration - Perfectly Centered -->
        <div style="display: flex; justify-content: center; margin-bottom: 5rem;" data-aos="fade-up" data-aos-delay="200">
            <img src="assets/images/help.jpg" 
                 alt="Students training together in a welcoming dojo"
                 style="width: 100%; max-width: 600px; height: auto; border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); object-fit: cover;">
        </div>
        <p class="text-center" style="font-size: 1rem; color: var(--color-muted-foreground); font-style: italic; margin-bottom: 4rem;">
            We're here to guide you every step of the way 🥋
        </p>


        <!-- FAQs -->
        <div style="max-width: 900px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="400">
            <h2 class="text-center section-title" style="font-size: 2.5rem; margin-bottom: 3rem;">
                <span style="color: var(--color-foreground);">FREQUENTLY ASKED</span>
                <span class="gradient-text">QUESTIONS</span>
            </h2>

            <div id="faqContainer">
                <?php foreach ($faqs as $index => $faq): ?>
                <div class="glass-card faq-item hover-lift" 
                     style="margin-bottom: 1rem;"
                     id="faq-<?php echo $index + 1; ?>"
                     data-question="<?php echo strtolower($faq['question']); ?>"
                     data-answer="<?php echo strtolower($faq['answer']); ?>">
                    <button class="faq-question" 
                            style="width: 100%; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; text-align: left; background: none; border: none; cursor: pointer;">
                        <span style="font-weight: 600; font-size: 1.125rem; color: var(--color-foreground);"><?php echo $faq['question']; ?></span>
                        <i class="fas fa-chevron-down faq-icon" style="font-size: 1.25rem; color: var(--color-primary); transition: transform var(--transition-base);"></i>
                    </button>
                    <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease;">
                        <div style="padding: 0 1.5rem 1.5rem; color: var(--color-muted-foreground); line-height: 1.7;">
                            <?php echo nl2br($faq['answer']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="noFaqResults" style="display: none; text-align: center; padding: 4rem 0;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--color-muted); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--color-muted-foreground);">No results found</h3>
                <p style="color: var(--color-muted-foreground);">Try different keywords or contact us below.</p>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="text-center" style="margin-top: 6rem;" data-aos="fade-up" data-aos-delay="500">
            <div class="glass-card" style="padding: 3rem; max-width: 700px; margin: 0 auto;">
                <i class="fas fa-headset" style="font-size: 3rem; color: var(--color-primary); margin-bottom: 1.5rem;"></i>
                <h3 class="section-title" style="font-size: 2rem; margin-bottom: 1rem;">Still Need Help?</h3>
                <p style="color: var(--color-muted-foreground); max-width: 500px; margin: 0 auto 2rem; font-size: 1.125rem;">
                    Our team is ready to answer any questions.
                </p>
                <a href="contact.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Script (unchanged) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    const searchInput = document.getElementById('helpSearch');
    const faqContainer = document.getElementById('faqContainer');
    const noResults = document.getElementById('noFaqResults');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon');

        question.addEventListener('click', () => {
            const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

            faqItems.forEach(other => {
                if (other !== item) {
                    other.querySelector('.faq-answer').style.maxHeight = '0';
                    other.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
                }
            });

            if (isOpen) {
                answer.style.maxHeight = '0';
                icon.style.transform = 'rotate(0deg)';
            } else {
                answer.style.maxHeight = answer.scrollHeight + 20 + 'px';
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });

    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        let visible = 0;

        faqItems.forEach(item => {
            const q = item.getAttribute('data-question');
            const a = item.getAttribute('data-answer');
            const matches = term === '' || q.includes(term) || a.includes(term);
            item.style.display = matches ? '' : 'none';
            if (matches) visible++;
        });

        faqContainer.style.display = visible === 0 && term !== '' ? 'none' : 'block';
        noResults.style.display = visible === 0 && term !== '' ? 'block' : 'none';
    });
});
</script>

<style>
.faq-item:hover { box-shadow: var(--shadow-lg); }
.faq-question:hover span { color: var(--color-primary); }
</style>

<?php require_once 'includes/footer.php'; ?>