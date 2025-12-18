<?php
/**
 * FindMyDojo - Header & Navigation
 * This file contains the HTML head, meta tags, and navigation bar
 */

// Include configuration
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Find the best martial arts dojos near you. Explore karate, jiu-jitsu, muay thai, taekwondo schools. Connect with masters and join a global martial arts community.'; ?>">
    <meta name="keywords" content="martial arts, dojo, karate, jiu-jitsu, muay thai, taekwondo, MMA, kung fu, training, fitness">
    <meta name="author" content="FindMyDojo">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>">
    <meta property="og:description" content="Find the best martial arts dojos near you. Connect with masters and join a global martial arts community.">
    <meta property="og:type" content="website">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>main.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>components.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>responsive.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Noise Overlay -->
    <div class="noise-overlay"></div>
    
    <!-- Navigation Header -->
    <header class="navbar" id="navbar">
        <nav class="nav-container">
            <div class="nav-content">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <span class="logo-icon">🐉</span>
                    <span class="logo-text"><?php echo SITE_NAME; ?></span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="nav-links desktop-nav">
                    <?php foreach ($nav_links as $link): ?>
                        <a href="<?php echo $link['path']; ?>" 
                           class="nav-link <?php echo is_active($link['path']); ?>">
                            <?php echo $link['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Desktop Actions -->
                <div class="nav-actions desktop-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Logged In -->
                        <div style="display: flex; align-items: center; gap: 1rem; margin-left: 1rem;">
                            <span style="color: var(--color-foreground); font-weight: 500;">
                                Hi, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?>!
                            </span>
                            <a href="logout.php" class="btn btn-primary">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Not Logged In -->
                        <a href="auth.php" class="btn btn-outline">Login</a>
                        <a href="auth.php?mode=register" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
                
            </div>
        </nav>
    </header>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">