<?php
/**
 * Maintenance Mode Admin Interface
 * Niche Society Website
 */

// Start session for authentication
session_start();

// Define essential constants first
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
    define('CONTACT_PHONE', '+966 1 1 296 7735');
}

// Load configuration without database dependency
$config_file = __DIR__ . '/../config/config.php';
if (!file_exists($config_file)) {
    // Define essential constants directly without eval()
    // These are the key constants needed for maintenance admin
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
        error_log("Config loading error (non-critical for maintenance admin): " . $e->getMessage());
    }
}

// Load helpers if available
$helpers_file = __DIR__ . '/../functions/helpers.php';
if (file_exists($helpers_file)) {
    require_once $helpers_file;
}

// Load admin settings
$admin_settings_file = __DIR__ . '/../config/admin-settings.php';
$maintenance_settings = [];
$site_settings = [];
$admin_credentials = [];

if (file_exists($admin_settings_file)) {
    include $admin_settings_file;
}

// Handle login/logout
if (isset($_POST['logout'])) {
    unset($_SESSION['admin_authenticated']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_credentials['username'] && $password === $admin_credentials['password']) {
        $_SESSION['admin_authenticated'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

// Check authentication
$authenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;

// Handle form submissions
if ($authenticated && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_maintenance'])) {
        $maintenance_settings['enabled'] = isset($_POST['maintenance_enabled']);
        $maintenance_settings['message'] = $_POST['maintenance_message'] ?? '';
        $maintenance_settings['start_time'] = $maintenance_settings['enabled'] ? date('Y-m-d H:i:s') : null;
        $maintenance_settings['end_time'] = null;
        
        // Update admin settings file
        $content = "<?php\n/**\n * Admin Settings Configuration\n * Generated on: " . date('Y-m-d H:i:s') . "\n */\n\n";
        $content .= "\$maintenance_settings = " . var_export($maintenance_settings, true) . ";\n\n";
        $content .= "\$site_settings = " . var_export($site_settings, true) . ";\n\n";
        $content .= "\$admin_credentials = " . var_export($admin_credentials, true) . ";\n\n";
        
        if (file_put_contents($admin_settings_file, $content) !== false) {
            $success = 'Maintenance settings updated successfully';
        } else {
            $error = 'Failed to update settings';
        }
    }
    
    if (isset($_POST['update_contact'])) {
        $site_settings['admin_email'] = $_POST['admin_email'] ?? '';
        $site_settings['company_phone'] = $_POST['company_phone'] ?? '';
        $site_settings['company_name'] = $_POST['company_name'] ?? '';
        
        // Update admin settings file
        $content = "<?php\n/**\n * Admin Settings Configuration\n * Generated on: " . date('Y-m-d H:i:s') . "\n */\n\n";
        $content .= "\$maintenance_settings = " . var_export($maintenance_settings, true) . ";\n\n";
        $content .= "\$site_settings = " . var_export($site_settings, true) . ";\n\n";
        $content .= "\$admin_credentials = " . var_export($admin_credentials, true) . ";\n\n";
        
        if (file_put_contents($admin_settings_file, $content) !== false) {
            $success = 'Contact information updated successfully';
        } else {
            $error = 'Failed to update contact information';
        }
    }
}

// Get current maintenance status
$maintenance_enabled = $maintenance_settings['enabled'] ?? false;
$maintenance_message = $maintenance_settings['message'] ?? 'We are currently performing scheduled maintenance to improve your experience.';
$admin_email = $site_settings['admin_email'] ?? CONTACT_EMAIL;
$company_phone = $site_settings['company_phone'] ?? CONTACT_PHONE;
$company_name = $site_settings['company_name'] ?? SITE_NAME;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Maintenance Admin - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;500;600;700&family=Arimo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arimo', sans-serif;
            background: linear-gradient(135deg, #602234 0%, #8b3954 50%, #a0586d 100%);
            min-height: 100vh;
            margin: 0;
            padding: 2rem 0;
            color: #fffaf3;
        }
        
        .admin-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .admin-card {
            background: rgba(255, 250, 243, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 250, 243, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .brand-title {
            font-family: 'Domine', serif;
            font-weight: 700;
            color: #d4af37;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .section-title {
            font-family: 'Domine', serif;
            font-weight: 600;
            color: #fffaf3;
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            background: rgba(255, 250, 243, 0.1);
            border: 1px solid rgba(255, 250, 243, 0.3);
            color: #fffaf3;
            border-radius: 10px;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 250, 243, 0.15);
            border-color: #d4af37;
            color: #fffaf3;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 250, 243, 0.6);
        }
        
        .btn-primary {
            background: #d4af37;
            border-color: #d4af37;
            color: #602234;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.75rem 2rem;
        }
        
        .btn-primary:hover {
            background: #b8941f;
            border-color: #b8941f;
            color: #602234;
        }
        
        .btn-secondary {
            background: rgba(255, 250, 243, 0.1);
            border: 1px solid rgba(255, 250, 243, 0.3);
            color: #fffaf3;
            border-radius: 10px;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 250, 243, 0.2);
            color: #fffaf3;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-enabled {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        
        .status-disabled {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .form-check-input:checked {
            background-color: #d4af37;
            border-color: #d4af37;
        }
        
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php if (!$authenticated): ?>
        <!-- Login Form -->
        <div class="admin-card">
            <div class="text-center mb-4">
                <h1 class="brand-title mb-3">Admin Access</h1>
                <p class="opacity-80">Please login to manage maintenance mode</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" class="login-form">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </div>
            </form>
        </div>
        
        <?php else: ?>
        <!-- Admin Interface -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="brand-title mb-2">Maintenance Admin</h1>
                    <p class="opacity-80 mb-0">Manage website maintenance mode and settings</p>
                </div>
                <form method="post" class="mb-0">
                    <button type="submit" name="logout" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
            
            <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Maintenance Status -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">
                    <i class="bi bi-gear me-2"></i>Maintenance Status
                </h2>
                <span class="status-badge <?php echo $maintenance_enabled ? 'status-enabled' : 'status-disabled'; ?>">
                    <?php echo $maintenance_enabled ? 'ENABLED' : 'DISABLED'; ?>
                </span>
            </div>
            
            <form method="post">
                <div class="mb-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="maintenance_enabled" name="maintenance_enabled" 
                               <?php echo $maintenance_enabled ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="maintenance_enabled">
                            <strong>Enable Maintenance Mode</strong>
                            <div class="text-muted small mt-1">
                                When enabled, visitors will see the maintenance page
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="maintenance_message" class="form-label">Maintenance Message</label>
                    <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3" 
                              placeholder="Enter custom maintenance message..."><?php echo htmlspecialchars($maintenance_message); ?></textarea>
                    <div class="text-muted small mt-1">
                        This message will be displayed to visitors during maintenance
                    </div>
                </div>
                
                <button type="submit" name="toggle_maintenance" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Update Maintenance Settings
                </button>
            </form>
        </div>
        
        <!-- Contact Information -->
        <div class="admin-card">
            <h2 class="section-title mb-4">
                <i class="bi bi-telephone me-2"></i>Contact Information
            </h2>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                               value="<?php echo htmlspecialchars($admin_email); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="company_phone" class="form-label">Company Phone</label>
                        <input type="tel" class="form-control" id="company_phone" name="company_phone" 
                               value="<?php echo htmlspecialchars($company_phone); ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" 
                           value="<?php echo htmlspecialchars($company_name); ?>" required>
                </div>
                
                <button type="submit" name="update_contact" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Update Contact Information
                </button>
            </form>
        </div>
        
        <!-- Quick Links -->
        <div class="admin-card">
            <h2 class="section-title mb-4">
                <i class="bi bi-link-45deg me-2"></i>Quick Links
            </h2>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="<?php echo SITE_URL; ?>/maintenance.php" class="btn btn-secondary w-100" target="_blank">
                        <i class="bi bi-eye me-2"></i>View Maintenance Page
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary w-100" target="_blank">
                        <i class="bi bi-house me-2"></i>Visit Homepage
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
