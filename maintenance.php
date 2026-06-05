<?php
/**
 * Maintenance Mode Page - Niche Society
 */

// Load site configuration (defines constants once)
$config_file = __DIR__ . '/config/config.php';
if (!file_exists($config_file)) {
    if (!defined('SITE_NAME')) {
        define('SITE_NAME', 'Niche Society');
    }
    if (!defined('SITE_URL')) {
        define('SITE_URL', 'http://localhost/niche-society-main');
    }
    if (!defined('ASSETS_URL')) {
        define('ASSETS_URL', SITE_URL . '/assets');
    }
    if (!defined('CONTACT_EMAIL')) {
        define('CONTACT_EMAIL', 'info@niche-society.com');
    }
    if (!defined('CONTACT_PHONE')) {
        define('CONTACT_PHONE', '+966532447976');
    }
    // Define essential constants directly without eval()
    // These are the key constants needed for maintenance page
    if (!defined('DEFAULT_LANG')) define('DEFAULT_LANG', 'ar');
    if (!defined('AVAILABLE_LANGS')) define('AVAILABLE_LANGS', ['ar', 'en']);
if (!defined('SUPPORTED_LANGUAGES')) define('SUPPORTED_LANGUAGES', ['ar', 'en']);
    if (!defined('CONTACT_ADDRESS_AR')) define('CONTACT_ADDRESS_AR', 'الرياض، المملكة العربية السعودية');
    if (!defined('CONTACT_ADDRESS_EN')) define('CONTACT_ADDRESS_EN', 'Riyadh, Saudi Arabia');
    if (!defined('SOCIAL_FACEBOOK')) define('SOCIAL_FACEBOOK', 'https://facebook.com/nichesociety');
    if (!defined('SOCIAL_TWITTER')) define('SOCIAL_TWITTER', 'https://twitter.com/nichesociety');
    if (!defined('SOCIAL_INSTAGRAM')) define('SOCIAL_INSTAGRAM', 'https://instagram.com/nichesociety');
    if (!defined('SOCIAL_LINKEDIN')) define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/nichesociety');
} else {
    try {
        require_once $config_file;
    } catch (Exception $e) {
        error_log("Config loading error (non-critical for maintenance page): " . $e->getMessage());
    }
}

// Load helpers if available
$helpers_file = __DIR__ . '/functions/helpers.php';
if (file_exists($helpers_file)) {
    require_once $helpers_file;
}

// Load admin settings for dynamic contact information
$admin_settings_file = __DIR__ . '/config/admin-settings.php';
$site_settings = [];
$maintenance_settings = [];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

// Use admin settings if available, otherwise fall back to defaults
$admin_email = isset($site_settings['admin_email']) ? $site_settings['admin_email'] : CONTACT_EMAIL;
$company_phone = isset($site_settings['company_phone']) ? $site_settings['company_phone'] : CONTACT_PHONE;
$company_name = isset($site_settings['company_name']) ? $site_settings['company_name'] : SITE_NAME;

// Maintenance message
$maintenance_message = isset($maintenance_settings['message']) ? $maintenance_settings['message'] : 'We are currently performing scheduled maintenance to improve your experience.';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Maintenance Mode - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;500;600;700&family=Arimo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    
    <style>
        body {
            font-family: 'Arimo', sans-serif;
            background: linear-gradient(135deg, #602234 0%, #8b3954 50%, #a0586d 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #fffaf3;
        }
        
        .maintenance-container {
            animation: fadeIn 1.5s ease-in;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .gear {
            animation: spin 8s linear infinite;
            color: #d4af37;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .maintenance-card {
            background: rgba(255, 250, 243, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 250, 243, 0.2);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .brand-title {
            font-family: 'Arimo', sans-serif;
            font-weight: 700;
            color: #d4af37;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .section-title {
            font-family: 'Arimo', sans-serif;
            font-weight: 600;
            color: #fffaf3;
        }
        
        .contact-link {
            color: #fffaf3;
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        
        .contact-link:hover {
            color: #d4af37;
            border-bottom-color: #d4af37;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: #d4af37;
        }
        
        .admin-link {
            color: rgba(255, 250, 243, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .admin-link:hover {
            color: #d4af37;
        }
        
        .badge-year {
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #d4af37;
        }
        
        .brand-logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="maintenance-card animate__animated animate__fadeIn">
                        <!-- Brand Badge -->
                        <div class="text-center mb-4">
                            <div class="badge-year d-inline-block">
                                EST. 2025
                            </div>
                        </div>
                        
                        <!-- Company Logo -->
                        <div class="text-center mb-4">
                            <img src="<?php echo ASSETS_URL; ?>/images/<?php echo urlencode('logo 2.png'); ?>" alt="<?php echo htmlspecialchars(SITE_NAME); ?>" class="brand-logo" onerror="this.src='<?php echo ASSETS_URL; ?>/images/logo.png';">
                        </div>
                        
                        <!-- Maintenance Icon -->
                        <div class="text-center mb-5">
                            <div class="d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: rgba(255, 250, 243, 0.1); border-radius: 50%; backdrop-filter: blur(10px);">
                                <i class="bi bi-gear-fill gear" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Maintenance Message -->
                        <h2 class="section-title text-center mb-4" style="font-size: 2rem;">
                            Under Maintenance
                        </h2>
                        
                        <p class="text-center mb-5" style="font-size: 1.2rem; opacity: 0.9;">
                            <?php echo htmlspecialchars($maintenance_message); ?>
                        </p>
                        
                        <!-- Features Grid -->
                        <div class="row mb-5">
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <div class="feature-icon">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <h4 class="section-title mb-2">System Updates</h4>
                                    <p style="opacity: 0.8; font-size: 0.9rem;">
                                        Enhancing performance and security
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <div class="feature-icon">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <h4 class="section-title mb-2">Brief Downtime</h4>
                                    <p style="opacity: 0.8; font-size: 0.9rem;">
                                        Expected completion shortly
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <div class="feature-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <h4 class="section-title mb-2">Data Protected</h4>
                                    <p style="opacity: 0.8; font-size: 0.9rem;">
                                        Your information remains secure
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="text-center mb-4 p-4" style="background: rgba(255, 250, 243, 0.05); border-radius: 15px; border: 1px solid rgba(255, 250, 243, 0.1);">
                            <h4 class="section-title mb-3">Need Immediate Assistance?</h4>
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-4">
                                <a href="tel:<?php echo str_replace([' ', '+'], ['', ''], $company_phone); ?>" class="contact-link d-flex align-items-center gap-2">
                                    <i class="bi bi-telephone"></i>
                                    <?php echo htmlspecialchars($company_phone); ?>
                                </a>
                                <span class="d-none d-md-block" style="opacity: 0.5;">|</span>
                                <a href="mailto:<?php echo htmlspecialchars($admin_email); ?>" class="contact-link d-flex align-items-center gap-2">
                                    <i class="bi bi-envelope"></i>
                                    <?php echo htmlspecialchars($admin_email); ?>
                                </a>
                            </div>
                        </div>
                        
                                            </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>
