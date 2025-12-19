<?php
/**
 * FindMyDojo - Configuration File
 * This file contains site-wide configuration settings
 */

<?php
// Load .env file for secure credentials management
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $config = parse_ini_file($envFile);
    if ($config === false) {
        die("Error loading .env file.");
    }
    define('DB_HOST', $config['DB_HOST'] ?? 'localhost');
    define('DB_USER', $config['DB_USER'] ?? 'ramatou.hassane');
    define('DB_PASS', $config['DB_PASS'] ?? 'H00pla%a');
    define('DB_NAME', $config['DB_NAME'] ?? 'webtech_2025A_ramatou_hassane');
} else {
    die(".env file not found.");
}

// Rest of your config.php (database connection, site settings, etc.)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Path Configuration
define('CSS_PATH', SITE_URL . '/css/');
define('JS_PATH', SITE_URL . '/js/');
define('ASSETS_PATH', SITE_URL . '/assets/');
define('INCLUDES_PATH', __DIR__);

// Navigation Links
$nav_links = [
    ['name' => 'Home', 'path' => 'index.php'],
    ['name' => 'Dojos', 'path' => 'dojos.php'],
    ['name' => 'Styles', 'path' => 'dojos.php'],
    ['name' => 'Events', 'path' => 'events.php'],
    ['name' => 'About', 'path' => 'help.php'],
];

// Footer Links
$footer_links = [
    'platform' => [
        ['name' => 'Find Dojos', 'path' => 'dojos.php'],
        ['name' => 'Events Calendar', 'path' => 'events.php'],
        ['name' => 'Martial Arts Styles', 'path' => 'dojos.php'],
        ['name' => 'Dojo Owners', 'path' => 'auth.php'],
        ['name' => 'Analytics', 'path' => 'help.php'],
    ],
    'company' => [
        ['name' => 'About Us', 'path' => 'help.php'],
        ['name' => 'Careers', 'path' => 'help.php'],
        ['name' => 'Contact', 'path' => 'contact.php'],
        ['name' => 'Help Center', 'path' => 'help.php'],
        ['name' => 'Privacy Policy', 'path' => 'help.php'],
    ]
];

// Social Media Links
$social_links = [
    ['platform' => 'Facebook', 'url' => '#', 'icon' => 'facebook'],
    ['platform' => 'Twitter', 'url' => '#', 'icon' => 'twitter'],
    ['platform' => 'Instagram', 'url' => '#', 'icon' => 'instagram'],
    ['platform' => 'LinkedIn', 'url' => '#', 'icon' => 'linkedin'],
];

// Helper function to get current page
function get_current_page() {
    return basename($_SERVER['PHP_SELF']);
}

// Helper function to check if link is active
function is_active($path) {
    return get_current_page() === $path ? 'active' : '';
}

// Security: Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function get_csrf_token() {
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>