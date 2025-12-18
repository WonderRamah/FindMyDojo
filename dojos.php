<?php
/**
 * FindMyDojo - Dojos Page (Database Connected)
 * Browse and search martial arts dojos worldwide
 */

// Page-specific variables
$page_title = 'Find Dojos';
$page_description = 'Browse through thousands of verified martial arts schools worldwide. Search by location, style, and more.';
$page_scripts = ['forms.js'];

// Include header
require_once 'includes/header.php';

// Fetch dojos from database with related data
try {
    $query = "
        SELECT 
            d.dojo_id,
            d.dojo_name,
            d.description,
            d.address,
            d.phone,
            d.email,
            d.website,
            d.dojo_image,
            c.city_name,
            co.country_name,
            co.country_code,
            GROUP_CONCAT(s.style_name SEPARATOR ', ') as styles,
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(DISTINCT r.review_id) as review_count
        FROM dojos d
        INNER JOIN cities c ON d.city_id = c.city_id
        INNER JOIN countries co ON c.country_id = co.country_id
        LEFT JOIN dojo_styles ds ON d.dojo_id = ds.dojo_id
        LEFT JOIN styles s ON ds.style_id = s.style_id
        LEFT JOIN reviews r ON d.dojo_id = r.dojo_id
        WHERE d.is_approved = 1
        GROUP BY d.dojo_id
        ORDER BY avg_rating DESC, d.dojo_name ASC
    ";
    
    $stmt = $pdo->query($query);
    $dojos = $stmt->fetchAll();
    
    // Fetch all styles for filter
    $stylesQuery = "SELECT style_id, style_name FROM styles ORDER BY style_name";
    $stylesStmt = $pdo->query($stylesQuery);
    $styles = $stylesStmt->fetchAll();
    
    // Fetch all countries for filter
    $countriesQuery = "SELECT country_id, country_name FROM countries ORDER BY country_name";
    $countriesStmt = $pdo->query($countriesQuery);
    $countries = $countriesStmt->fetchAll();
    
} catch (PDOException $e) {
    die("Error fetching dojos: " . $e->getMessage());
}
?>

<div style="padding-top: 8rem; padding-bottom: 6rem; background-color: var(--color-background);">
    <div class="container">
        <!-- Page Header -->
        <div class="text-center" style="margin-bottom: 3rem;" data-aos="fade-up">
            <h1 class="section-title" style="margin-bottom: 1rem;">Find Your Dojo</h1>
            <p class="section-description">Browse through verified martial arts schools worldwide</p>
        </div>

        <!-- Search & Filter Bar -->
        <div class="glass-card" style="padding: 1rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="100">
            <form class="grid" style="grid-template-columns: 1fr auto auto auto; gap: 1rem; align-items: center;" data-search-form>
                <!-- Search Input -->
                <div style="position: relative;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.5);">
                        <i class="fas fa-search" style="color: var(--color-muted-foreground);"></i>
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Search by name, style, or location..." 
                            style="flex: 1; background: transparent; border: none; outline: none; color: var(--color-foreground);"
                        >
                        <button type="button" data-search-clear style="display: none; background: none; border: none; color: var(--color-muted-foreground); cursor: pointer;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Style Filter -->
                <div style="position: relative;">
                    <select 
                        id="styleFilter" 
                        data-filter="style" 
                        data-filter-target="#dojosGrid"
                        style="padding: 0.875rem 2.5rem 0.875rem 1rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.5); border: 1px solid var(--color-border); color: var(--color-foreground); cursor: pointer; appearance: none;"
                    >
                        <option value="all">All Styles</option>
                        <?php foreach ($styles as $style): ?>
                            <option value="<?php echo strtolower($style['style_name']); ?>">
                                <?php echo htmlspecialchars($style['style_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--color-muted-foreground);"></i>
                </div>

                <!-- Country Filter -->
                <div style="position: relative;">
                    <select 
                        id="countryFilter" 
                        data-filter="country" 
                        data-filter-target="#dojosGrid"
                        style="padding: 0.875rem 2.5rem 0.875rem 1rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.5); border: 1px solid var(--color-border); color: var(--color-foreground); cursor: pointer; appearance: none;"
                    >
                        <option value="all">All Countries</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo strtolower($country['country_name']); ?>">
                                <?php echo htmlspecialchars($country['country_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--color-muted-foreground);"></i>
                </div>

                <!-- Search Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </form>
        </div>

        <!-- Results Count -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <p style="color: var(--color-muted-foreground);">
                Showing <span id="resultsCount" style="color: var(--color-foreground); font-weight: 600;"><?php echo count($dojos); ?></span> dojos
            </p>
        </div>

        <!-- Dojos Grid -->
        <div id="dojosGrid" class="grid grid-cols-3" style="gap: 1.5rem;">
            <?php if (empty($dojos)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🥋</div>
                    <h3 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 0.5rem;">No dojos found</h3>
                    <p style="color: var(--color-muted-foreground);">Be the first to add a dojo!</p>
                </div>
            <?php else: ?>
                <?php foreach ($dojos as $index => $dojo): ?>
                <div class="dojo-card hover-lift" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>"
                     data-filter-item
                     data-style="<?php echo strtolower($dojo['styles'] ?? ''); ?>"
                     data-country="<?php echo strtolower($dojo['country_name']); ?>"
                     data-name="<?php echo strtolower($dojo['dojo_name']); ?>"
                     data-location="<?php echo strtolower($dojo['city_name']); ?>">
                    
                    <a href="dojo_detail.php?id=<?php echo $dojo['dojo_id']; ?>" style="display: block; text-decoration: none; color: inherit;">
                    <!-- Dojo Image -->
                    <div class="dojo-image">
                        <?php 
                        $dojoCardImg = !empty($dojo['dojo_image']) 
                            ? 'assets/images/dojos/' . htmlspecialchars($dojo['dojo_image'])
                            : 'https://thumbs.dreamstime.com/z/professional-martial-arts-dojo-interior-stunning-training-space-bright-sharp-lighting-pristine-tatami-mats-step-373023527.jpg';  // Beautiful clean dojo fallback
                        ?>
                        <img src="<?php echo $dojoCardImg; ?>" 
                            alt="<?php echo htmlspecialchars($dojo['dojo_name']); ?>"
                            style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="dojo-image-overlay"></div>
                        <div class="dojo-rating">
                            <i class="fas fa-star" style="color: var(--color-primary);"></i>
                            <span><?php echo number_format($dojo['avg_rating'], 1); ?></span>
                        </div>
                    </div>
        

                        <!-- Dojo Content -->
                        <div class="dojo-content">
                            <div class="dojo-style"><?php echo htmlspecialchars($dojo['styles'] ?? 'Various Styles'); ?></div>
                            <h3 class="dojo-name"><?php echo htmlspecialchars($dojo['dojo_name']); ?></h3>
                            <div class="dojo-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($dojo['city_name'] . ', ' . $dojo['country_name']); ?></span>
                            </div>
                            <?php if ($dojo['description']): ?>
                            <p style="color: var(--color-muted-foreground); font-size: 0.875rem; margin: 0.5rem 0;">
                                <?php echo htmlspecialchars(substr($dojo['description'], 0, 100)) . '...'; ?>
                            </p>
                            <?php endif; ?>
                            <div class="dojo-footer">
                                <span><?php echo $dojo['review_count']; ?> reviews</span>
                                <span>
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($dojo['phone'] ?? 'N/A'); ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- No Results Message -->
        <div id="noResults" style="display: none; text-align: center; padding: 4rem 0;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🥋</div>
            <h3 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 0.5rem;">No dojos found</h3>
            <p style="color: var(--color-muted-foreground); margin-bottom: 1.5rem;">Try adjusting your search or filters</p>
            <button class="btn btn-outline" onclick="clearFilters()">Clear Filters</button>
        </div>
    </div>
</div>

<!-- Custom JavaScript for this page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const styleFilter = document.getElementById('styleFilter');
    const countryFilter = document.getElementById('countryFilter');
    const dojosGrid = document.getElementById('dojosGrid');
    const resultsCount = document.getElementById('resultsCount');
    const noResults = document.getElementById('noResults');
    const dojoCards = document.querySelectorAll('[data-filter-item]');

    // Combined filter function
    function filterDojos() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStyle = styleFilter.value.toLowerCase();
        const selectedCountry = countryFilter.value.toLowerCase();
        
        let visibleCount = 0;

        dojoCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const location = card.getAttribute('data-location');
            const style = card.getAttribute('data-style');
            const country = card.getAttribute('data-country');

            // Check search term
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                location.includes(searchTerm) || 
                style.includes(searchTerm);

            // Check style filter
            const matchesStyle = selectedStyle === 'all' || style.includes(selectedStyle);

            // Check country filter
            const matchesCountry = selectedCountry === 'all' || country === selectedCountry;

            // Show/hide card
            if (matchesSearch && matchesStyle && matchesCountry) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update results count
        resultsCount.textContent = visibleCount;

        // Show/hide no results message
        if (visibleCount === 0) {
            dojosGrid.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            dojosGrid.style.display = 'grid';
            noResults.style.display = 'none';
        }
    }

    // Add event listeners
    searchInput.addEventListener('input', filterDojos);
    styleFilter.addEventListener('change', filterDojos);
    countryFilter.addEventListener('change', filterDojos);

    // Clear filters function
    window.clearFilters = function() {
        searchInput.value = '';
        styleFilter.value = 'all';
        countryFilter.value = 'all';
        filterDojos();
    };
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>