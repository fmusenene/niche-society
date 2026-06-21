<?php
/**
 * Site CMS Admin — maintenance, services, about, contact
 */
require_once __DIR__ . '/bootstrap.php';

$adminBootstrapError = defined('ADMIN_BOOTSTRAP_ERROR') ? ADMIN_BOOTSTRAP_ERROR : null;

$error = null;
$loginSuccess = null;
$view = $_GET['view'] ?? 'login';
$resetToken = trim($_GET['token'] ?? '');
if ($resetToken !== '') {
    $view = 'reset';
}
$resetTokenValid = $resetToken !== '' && adminFindValidReset($pdo, $resetToken);

if (isset($_POST['logout'])) {
    unset($_SESSION['admin_authenticated'], $_SESSION['admin_last_activity'], $_SESSION['admin_login_at']);
    adminRedirect();
}

if (isset($_POST['login'])) {
    $result = adminAttemptLogin(
        $pdo,
        trim($_POST['username'] ?? ''),
        $_POST['password'] ?? '',
        $maintenance_settings,
        $site_settings,
        $admin_credentials
    );
    if ($result['ok']) {
        adminRedirect();
    }
    $error = $result['error'];
}

$authenticated = adminIsAuthenticated();
$loginLockedOut = !$authenticated && adminIsLoginLockedOut($pdo);

if (!$authenticated && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_reset'])) {
        if (!adminVerifyPublicCsrf($_POST['admin_public_csrf'] ?? null)) {
            $error = 'Security token expired. Please try again.';
            $view = 'forgot';
        } else {
            if (adminIsLoginLockedOut($pdo)) {
                $error = 'Too many failed attempts. Please wait before requesting a reset link.';
                $view = 'forgot';
            } else {
            $result = adminRequestPasswordReset($pdo, $_POST['email'] ?? '', $site_settings);
            if (!empty($result['error'])) {
                $error = $result['error'];
                $view = 'forgot';
            } else {
                $loginSuccess = $result['message'];
                $view = 'forgot';
            }
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        if (!adminVerifyPublicCsrf($_POST['admin_public_csrf'] ?? null)) {
            $error = 'Security token expired. Please try again.';
            $view = 'reset';
        } else {
            $result = adminCompletePasswordReset(
                $pdo,
                $_POST['token'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? '',
                $maintenance_settings,
                $site_settings,
                $admin_credentials
            );
            if ($result['ok']) {
                $loginSuccess = $result['message'];
                $view = 'login';
                $resetToken = '';
                $resetTokenValid = false;
            } else {
                $error = $result['error'];
                $view = 'reset';
                $resetToken = $_POST['token'] ?? '';
                $resetTokenValid = adminFindValidReset($pdo, $resetToken) !== null;
            }
        }
    }
}

$section = $_GET['section'] ?? 'dashboard';
$flash = adminGetFlash();

if ($authenticated && isset($_GET['seed'])) {
    require_once __DIR__ . '/seed-defaults.php';
    $force = !empty($_GET['force']);
    $count = cmsSeedDefaults($pdo, $force);
    adminFlash('success', $force
        ? "Synced all hardcoded content. Updated/added {$count} services."
        : "Imported {$count} new services (existing slugs were kept). Use “Sync all” to overwrite.");
    adminRedirect('section=services');
}

$aboutPage = cmsGetPage($pdo, 'about');
$servicesPage = cmsGetPage($pdo, 'services');
$contactSettings = array_merge(
    cmsGetSettingsByCategory($pdo, 'contact'),
    cmsGetSettingsByCategory($pdo, 'social'),
    cmsGetSettingsByCategory($pdo, 'company')
);
$allServices = $pdo->query('SELECT * FROM services ORDER BY display_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
$editService = null;
$openServiceModal = false;
if ($authenticated && $section === 'services') {
    if (!empty($_GET['add'])) {
        $openServiceModal = true;
    } elseif (!empty($_GET['edit'])) {
        $editId = (int) $_GET['edit'];
        foreach ($allServices as $s) {
            if ((int) $s['id'] === $editId) {
                $editService = $s;
                if (!empty($editService['page_data'])) {
                    $editService['detail'] = json_decode($editService['page_data'], true) ?: [];
                }
                $openServiceModal = true;
                break;
            }
        }
    }
}

$freshSettings = adminLoadSettingsFile();
$maintenance_settings = $freshSettings['maintenance_settings'];
$site_settings = $freshSettings['site_settings'];
$admin_credentials = $freshSettings['admin_credentials'];

$maintenance_enabled = !empty($maintenance_settings['enabled']);
$maintenance_message = $maintenance_settings['message'] ?? '';
$serviceCategories = $authenticated ? cmsGetServiceCategories($pdo) : [];
$activeServiceCount = count(array_filter($allServices, fn($s) => ($s['status'] ?? '') === 'active'));
$allInvoices = $authenticated ? cmsGetAllInvoices($pdo) : [];
$proposalInvoiceMap = $authenticated ? cmsGetProposalInvoiceMap($pdo) : [];
$invoiceStats = ['proposals' => 0, 'invoices' => 0, 'this_month' => 0, 'total_sar' => 0];
if ($authenticated) {
    $invoiceMonthPrefix = date('Y-m');
    foreach ($allInvoices as $inv) {
        if (cmsInvoiceIsProposal($inv)) {
            $invoiceStats['proposals']++;
        } else {
            $invoiceStats['invoices']++;
        }
        $updatedAt = (string) ($inv['updated_at'] ?? '');
        if ($updatedAt !== '' && strncmp($updatedAt, $invoiceMonthPrefix, 7) === 0) {
            $invoiceStats['this_month']++;
        }
        if (($inv['currency'] ?? 'SAR') === 'SAR') {
            $invoiceStats['total_sar'] += (int) ($inv['grand_total'] ?? 0);
        }
    }
}
$invoiceStats['count'] = $invoiceStats['proposals'];
$listProposals = array_values(array_filter($allInvoices, static fn(array $inv): bool => cmsInvoiceIsProposal($inv)));
$invoiceStats['invoiced'] = count(array_filter(
    $listProposals,
    static fn(array $inv): bool => isset($proposalInvoiceMap[(int) $inv['id']])
));
$openInvoiceBoot = null;
if ($authenticated && $section === 'invoices') {
    if (!empty($_GET['add'])) {
        $openInvoiceBoot = ['open' => 'new'];
    } elseif (!empty($_GET['edit'])) {
        $editInvoiceId = (int) $_GET['edit'];
        foreach ($allInvoices as $inv) {
            if ((int) $inv['id'] !== $editInvoiceId) {
                continue;
            }
            if (!cmsInvoiceIsProposal($inv)) {
                $sourceId = (int) ($inv['source_proposal_id'] ?? 0);
                if ($sourceId > 0) {
                    $editInvoiceId = $sourceId;
                    foreach ($allInvoices as $proposalRow) {
                        if ((int) $proposalRow['id'] === $sourceId) {
                            $inv = $proposalRow;
                            break;
                        }
                    }
                }
            }
            $openInvoiceBoot = [
                'open' => 'edit',
                'id' => $editInvoiceId,
                'subject' => $inv['subject'] ?: 'Untitled proposal',
                'number' => $inv['invoice_number'] ?? '',
            ];
            break;
        }
    }
}

$sectionTitles = [
    'dashboard' => 'Dashboard',
    'maintenance' => 'Maintenance mode',
    'services' => 'Services',
    'categories' => 'Service categories',
    'services_page' => 'Services listing page',
    'about' => 'About page',
    'contact' => 'Contact & company',
    'account' => 'Account & password',
    'invoices' => 'Proposals',
];
$pageHeading = $sectionTitles[$section] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($authenticated ? $pageHeading . ' — Site Admin' : 'Site Admin Login') ?> — <?= htmlspecialchars(SITE_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/admin.css?v=<?= (int) @filemtime(__DIR__ . '/assets/admin.css') ?>">
</head>
<body>
<span id="adminSiteUrl" data-base="<?= htmlspecialchars(rtrim(SITE_URL, '/')) ?>" hidden></span>
<?php if ($authenticated): ?>
<span id="adminCsrfToken" data-token="<?= htmlspecialchars(adminCsrfToken()) ?>" hidden></span>
<?php endif; ?>
<?php if (!$authenticated): ?>
<?php if ($adminBootstrapError): ?>
<div class="admin-login-page">
    <div class="admin-login-card">
        <div class="alert alert-danger">
            <strong>Admin setup error:</strong> <?= htmlspecialchars($adminBootstrapError) ?>
        </div>
        <p class="small text-muted mb-0">Check <code>logs/error.log</code> on the server, confirm <code>config/database.local.php</code> and <code>config/admin-settings.php</code>, then try
            <a href="?skip_auto_seed=1">skip auto-import</a>.</p>
    </div>
</div>
<?php else: ?>
<?php include __DIR__ . '/partials/login-forms.php'; ?>
<?php endif; ?>
<?php else: ?>
<div class="admin-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button type="button" class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h2 class="admin-topbar-title"><?= htmlspecialchars($pageHeading) ?></h2>
                    <p class="admin-topbar-sub mb-0"><?= htmlspecialchars(SITE_NAME) ?></p>
                </div>
            </div>
            <div class="translate-bar mb-0">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="autoTranslateToggle" checked>
                    <label class="form-check-label" for="autoTranslateToggle"><i class="bi bi-translate"></i> Auto-translate EN → AR</label>
                </div>
            </div>
        </header>
        <main class="admin-content">
            <?php if ($flash): ?>
            <?php
                $flashVariant = $flash['type'] === 'success' ? 'success' : 'danger';
                $flashIcon = $flashVariant === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill';
            ?>
            <div class="admin-flash-banner admin-flash-banner--<?= $flashVariant ?>" role="alert">
                <span class="admin-flash-banner__icon"><i class="bi <?= $flashIcon ?>" aria-hidden="true"></i></span>
                <span class="admin-flash-banner__text"><?= htmlspecialchars($flash['message']) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($section === 'dashboard'): ?>
            <div class="admin-stats-row">
                <div class="admin-stat-card">
                    <i class="bi bi-tools"></i>
                    <div class="admin-stat-value"><?= $maintenance_enabled ? 'ON' : 'OFF' ?></div>
                    <div class="admin-stat-label">Maintenance mode</div>
                </div>
                <div class="admin-stat-card">
                    <i class="bi bi-grid"></i>
                    <div class="admin-stat-value"><?= count($allServices) ?></div>
                    <div class="admin-stat-label">Total services</div>
                </div>
                <div class="admin-stat-card">
                    <i class="bi bi-check-circle"></i>
                    <div class="admin-stat-value"><?= $activeServiceCount ?></div>
                    <div class="admin-stat-label">Active services</div>
                </div>
                <div class="admin-stat-card">
                    <i class="bi bi-tags"></i>
                    <div class="admin-stat-value"><?= count($serviceCategories) ?></div>
                    <div class="admin-stat-label">Categories</div>
                </div>
            </div>
            <div class="card-admin">
                <p class="mb-3">Welcome to the Niche Society CMS. Manage site content, services, and maintenance from the sidebar.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="?section=services&seed=1&force=1" class="btn btn-primary btn-sm" onclick="return confirm('Import ALL 6 hardcoded services and update existing ones with full text from the website?');">Sync all hardcoded services</a>
                    <a href="?seed=1" class="btn btn-outline-primary btn-sm">Import missing only</a>
                </div>
                <p class="hint mt-2 mb-0">Use <strong>Sync all</strong> to load every service from the current website (6 services with full AR/EN text, features, and detail pages).</p>
                <div class="admin-quick-links">
                    <a href="?section=maintenance" class="admin-quick-link"><i class="bi bi-tools"></i> Maintenance settings</a>
                    <a href="?section=services" class="admin-quick-link"><i class="bi bi-grid"></i> Manage services</a>
                    <a href="?section=about" class="admin-quick-link"><i class="bi bi-info-circle"></i> Edit about page</a>
                    <a href="?section=contact" class="admin-quick-link"><i class="bi bi-envelope"></i> Contact & social</a>
                </div>
            </div>

            <?php elseif ($section === 'maintenance'): ?>
            <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="text-muted small mb-3">Control whether visitors see the maintenance page.</p>
                <div class="summary-row"><span class="summary-label">Status</span><span class="badge <?= $maintenance_enabled ? 'bg-warning text-dark' : 'bg-success' ?>"><?= $maintenance_enabled ? 'Enabled' : 'Disabled' ?></span></div>
                <div class="summary-row"><span class="summary-label">Message</span><span class="text-truncate" style="max-width:280px"><?= $maintenance_message ? htmlspecialchars(mb_strimwidth($maintenance_message, 0, 60, '…')) : '—' ?></span></div>
            </div>
            <div class="section-actions">
                <form method="post" action="actions.php" class="d-inline">
                    <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                    <input type="hidden" name="section" value="maintenance">
                    <input type="hidden" name="maintenance_message" value="<?= htmlspecialchars($maintenance_message) ?>">
                    <?php if (!$maintenance_enabled): ?>
                    <input type="hidden" name="maintenance_enabled" value="1">
                    <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-toggle-off"></i> Enable now</button>
                    <?php else: ?>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-toggle-on"></i> Disable now</button>
                    <?php endif; ?>
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalMaintenance"><i class="bi bi-pencil"></i> Edit settings</button>
                <a href="<?= url('maintenance.php') ?>" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-eye"></i> Preview</a>
            </div>
        </div>
            </div>

            <?php elseif ($section === 'contact'): ?>
            <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div class="flex-grow-1">
                <div class="summary-row"><span class="summary-label">Email</span><span><?= htmlspecialchars($contactSettings['site_email'] ?? CONTACT_EMAIL) ?></span></div>
                <div class="summary-row"><span class="summary-label">Phone</span><span><?= htmlspecialchars($contactSettings['site_phone'] ?? CONTACT_PHONE) ?></span></div>
                <div class="summary-row"><span class="summary-label">Company</span><span><?= htmlspecialchars($contactSettings['site_name_en'] ?? SITE_NAME) ?></span></div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalContact"><i class="bi bi-pencil"></i> Edit contact</button>
        </div>
        <hr>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <h3 class="h6 brand">Social & ISO</h3>
                <div class="summary-row"><span class="summary-label">Facebook</span><span class="text-truncate" style="max-width:220px"><?= htmlspecialchars($contactSettings['facebook_url'] ?? SOCIAL_FACEBOOK) ?></span></div>
                <div class="summary-row"><span class="summary-label">ISO</span><span><?= htmlspecialchars($contactSettings['iso_certificate'] ?? ISO_CERTIFICATE_NUMBER) ?></span></div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSocial"><i class="bi bi-pencil"></i> Edit social</button>
        </div>
    </div>

    <?php elseif ($section === 'about'): ?>
    <?php $ab = $aboutPage['sections'] ?? []; ?>
    <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="summary-row"><span class="summary-label">Hero (EN)</span><span><?= htmlspecialchars($ab['hero']['title_en'] ?? 'About Us') ?></span></div>
                <div class="summary-row"><span class="summary-label">Overview</span><span class="text-truncate" style="max-width:320px"><?= htmlspecialchars(mb_strimwidth($ab['overview']['lead_en'] ?? 'Not set', 0, 80, '…')) ?></span></div>
                <div class="summary-row"><span class="summary-label">Story (EN)</span><span><?= htmlspecialchars($ab['story']['title_en'] ?? '—') ?></span></div>
            </div>
            <div class="section-actions">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAbout"><i class="bi bi-pencil"></i> Edit content</button>
                <a href="<?= url('about.php') ?>" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-eye"></i> View page</a>
            </div>
        </div>
    </div>

    <?php elseif ($section === 'services_page'): ?>
    <?php $sp = $servicesPage['sections'] ?? []; ?>
    <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="summary-row"><span class="summary-label">Hero (EN)</span><span><?= htmlspecialchars($sp['hero']['title_en'] ?? 'Our Services') ?></span></div>
                <div class="summary-row"><span class="summary-label">Intro (EN)</span><span class="text-truncate" style="max-width:320px"><?= htmlspecialchars(mb_strimwidth($sp['intro']['lead_en'] ?? 'Not set', 0, 80, '…')) ?></span></div>
            </div>
            <div class="section-actions">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalServicesPage"><i class="bi bi-pencil"></i> Edit page</button>
                <a href="<?= url('services.php') ?>" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-eye"></i> View page</a>
            </div>
        </div>
    </div>

    <?php elseif ($section === 'categories'): ?>
    <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="text-muted small mb-2">Categories group services internally (e.g. for filters). Use short slugs like <code>luxury-travel</code>.</p>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($serviceCategories as $cat): ?>
                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($cat) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCategories"><i class="bi bi-pencil"></i> Edit categories</button>
        </div>
    </div>

    <?php elseif ($section === 'services'): ?>
    <div class="card-admin">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <p class="text-muted small mb-0"><?= count($allServices) ?> service(s) in database</p>
            <div class="section-actions">
                <a href="?section=services&add=1" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add service</a>
                <a href="?section=categories" class="btn btn-outline-secondary btn-sm"><i class="bi bi-tags"></i> Categories</a>
                <a href="?section=services&seed=1&force=1" class="btn btn-outline-primary btn-sm" onclick="return confirm('Reload all 6 services from website defaults?');">Sync defaults</a>
            </div>
        </div>
        <?php if (count($allServices) < 6): ?>
        <div class="alert alert-warning py-2">Only <?= count($allServices) ?> service(s) in database. Click <a href="?section=services&seed=1&force=1">Sync all hardcoded services</a> to import all 6 from the website.</div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>#</th><th>Title</th><th>Slug</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($allServices as $s): ?>
                <tr>
                    <td><?= (int)$s['display_order'] ?></td>
                    <td><?= htmlspecialchars($s['title_en']) ?></td>
                    <td><code><?= htmlspecialchars($s['slug']) ?></code></td>
                    <td><?= htmlspecialchars($s['status']) ?></td>
                    <td class="text-end text-nowrap">
                        <a href="?section=services&edit=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                        <a href="<?= url('service.php?slug=' . urlencode($s['slug'])) ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-eye"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-service"
                            data-id="<?= (int)$s['id'] ?>"
                            data-name="<?= htmlspecialchars($s['title_en'], ENT_QUOTES) ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php elseif ($section === 'account'): ?>
            <?php $recoveryEmail = adminGetRecoveryEmail($site_settings); ?>
            <div class="card-admin">
                <p class="text-muted small mb-3">Change your admin password without editing server files. If you forget it, log out and use <strong>Forgot password?</strong> on the login page.</p>
                <div class="summary-row mb-3"><span class="summary-label">Username</span><span><code><?= htmlspecialchars($admin_credentials['username'] ?? 'admin') ?></code></span></div>
                <div class="summary-row mb-4"><span class="summary-label">Recovery email</span><span><?= htmlspecialchars($recoveryEmail ?: 'Not set — configure under Contact') ?></span></div>
                <form method="post" action="actions.php" class="js-admin-password-form">
                    <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                    <input type="hidden" name="section" value="password">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Current password</label>
                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>New password</label>
                            <input type="password" name="new_password" class="form-control js-admin-new-password" required autocomplete="new-password">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Confirm new password</label>
                            <input type="password" name="confirm_password" class="form-control js-admin-confirm-password" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <?php include __DIR__ . '/partials/password-requirements.php'; ?>
                            <p class="js-admin-password-match admin-password-match small mb-3" aria-live="polite"></p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm js-admin-password-submit" disabled><i class="bi bi-key"></i> Update password</button>
                </form>
            </div>

    <?php elseif ($section === 'invoices'): ?>
    <div class="invoice-list-page">
        <div class="admin-stats-row invoice-stats-row">
            <div class="admin-stat-card invoice-stat-card">
                <i class="bi bi-file-earmark-text"></i>
                <div class="admin-stat-value" id="invoiceStatProposals"><?= (int) $invoiceStats['proposals'] ?></div>
                <div class="admin-stat-label">Proposals</div>
            </div>
            <div class="admin-stat-card invoice-stat-card">
                <i class="bi bi-receipt-cutoff"></i>
                <div class="admin-stat-value" id="invoiceStatInvoiced"><?= (int) ($invoiceStats['invoiced'] ?? 0) ?></div>
                <div class="admin-stat-label">Invoiced</div>
            </div>
            <div class="admin-stat-card invoice-stat-card invoice-stat-card--filter">
                <i class="bi bi-eye"></i>
                <div class="admin-stat-value" id="invoiceVisibleCount"><?= count($listProposals) ?></div>
                <div class="admin-stat-label">Showing results</div>
            </div>
            <div class="admin-stat-card invoice-stat-card">
                <i class="bi bi-calendar3"></i>
                <div class="admin-stat-value"><?= (int) $invoiceStats['this_month'] ?></div>
                <div class="admin-stat-label">Updated this month</div>
            </div>
            <?php if ($invoiceStats['total_sar'] > 0): ?>
            <div class="admin-stat-card invoice-stat-card">
                <i class="bi bi-cash-stack"></i>
                <div class="admin-stat-value"><?= number_format((int) $invoiceStats['total_sar']) ?></div>
                <div class="admin-stat-label">Total value (SAR)</div>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-admin invoice-list-card">
            <div class="invoice-list-toolbar">
                <div class="invoice-search-group">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="invoiceSearch" class="form-control" placeholder="Search…" autocomplete="off" aria-label="Search invoices">
                    </div>
                    <button type="button" class="invoice-search-clear" id="invoiceSearchClear" hidden aria-label="Clear search">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-primary btn-sm invoice-new-btn" id="btnNewInvoice" title="New proposal">
                    <i class="bi bi-plus-lg"></i><span class="invoice-new-btn-label"> New proposal</span>
                </button>
            </div>

            <?php if (count($listProposals) === 0): ?>
            <div class="invoice-empty-state">
                <div class="invoice-empty-state__icon"><i class="bi bi-file-earmark-text"></i></div>
                <h3 class="invoice-empty-state__title">No proposals yet</h3>
                <p class="invoice-empty-state__text">Create a Technical &amp; Financial Proposal to send to your client. When they accept, convert it to an invoice with one click.</p>
                <button type="button" class="btn btn-primary btn-sm invoice-empty-state__cta" id="btnNewInvoiceEmpty">
                    <i class="bi bi-plus-lg"></i> Create first proposal
                </button>
            </div>
            <?php else: ?>
            <div class="invoice-table-wrap table-responsive">
                <table class="table invoice-data-table mb-0" id="invoiceTable">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Invoice #</th>
                            <th>Subject</th>
                            <th>Offer date</th>
                            <th class="invoice-col-secondary">Event date</th>
                            <th class="invoice-col-secondary">Client</th>
                            <th class="text-end">Total</th>
                            <th class="invoice-col-secondary">Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTableBody">
                    <?php foreach ($listProposals as $inv):
                        $invSubject = $inv['subject'] ?: 'Untitled proposal';
                        $linkedInvoice = $proposalInvoiceMap[(int) $inv['id']] ?? null;
                        $linkedNumber = $linkedInvoice['invoice_number'] ?? '';
                        $searchBlob = strtolower(trim($invSubject . ' ' . ($inv['client_name'] ?? '') . ' ' . $linkedNumber));
                    ?>
                    <tr data-invoice-row data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES) ?>" data-record-type="proposal"<?= $linkedInvoice ? ' data-linked-invoice-id="' . (int) $linkedInvoice['id'] . '"' : '' ?>>
                        <td>
                            <?php if ($linkedInvoice): ?>
                            <span class="invoice-type-badge invoice-type-badge--proposal invoice-type-badge--linked">Invoiced</span>
                            <?php else: ?>
                            <span class="invoice-type-badge invoice-type-badge--proposal">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?php if ($linkedInvoice): ?><span class="invoice-num-badge"><?= htmlspecialchars($linkedNumber ?: '—') ?></span><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                        <td class="invoice-cell-subject"><?= htmlspecialchars($invSubject) ?></td>
                        <td class="text-nowrap"><?= htmlspecialchars(cmsInvoiceFormatDisplayDate($inv['offer_date'] ?? '', true)) ?></td>
                        <td class="text-nowrap invoice-col-secondary"><?= htmlspecialchars(cmsInvoiceFormatDisplayDate($inv['event_date'] ?? '', true)) ?></td>
                        <td class="invoice-col-secondary"><?= htmlspecialchars($inv['client_name'] ?: '—') ?></td>
                        <td class="text-end text-nowrap invoice-cell-total">
                            <strong><?= number_format((int) $inv['grand_total']) ?></strong>
                            <span class="invoice-cell-currency"><?= htmlspecialchars($inv['currency'] ?? 'SAR') ?></span>
                        </td>
                        <td class="text-nowrap text-muted invoice-col-secondary"><?= htmlspecialchars(date('Y-m-d', strtotime($inv['updated_at'] ?? 'now'))) ?></td>
                        <td class="text-end text-nowrap">
                            <div class="invoice-row-actions">
                                <?php if ($linkedInvoice): ?>
                                <button type="button" class="btn btn-sm btn-outline-info btn-print-linked-invoice"
                                    data-id="<?= (int) $linkedInvoice['id'] ?>"
                                    data-number="<?= htmlspecialchars($linkedNumber, ENT_QUOTES) ?>"
                                    title="Print invoice (choose installment)">
                                    <i class="bi bi-receipt"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-success btn-make-invoice"
                                    data-id="<?= (int) $inv['id'] ?>"
                                    data-subject="<?= htmlspecialchars($invSubject, ENT_QUOTES) ?>"
                                    title="Make invoice">
                                    <i class="bi bi-receipt-cutoff"></i>
                                </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-proposal"
                                    data-id="<?= (int) $inv['id'] ?>"
                                    data-subject="<?= htmlspecialchars($invSubject, ENT_QUOTES) ?>"
                                    title="Edit proposal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-print-invoice"
                                    data-id="<?= (int) $inv['id'] ?>"
                                    data-subject="<?= htmlspecialchars($invSubject, ENT_QUOTES) ?>"
                                    title="Print proposal">
                                    <i class="bi bi-printer"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-invoice"
                                    data-id="<?= (int) $inv['id'] ?>"
                                    data-name="<?= htmlspecialchars($invSubject, ENT_QUOTES) ?>"
                                    title="Delete invoice">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="invoiceSearchEmpty" hidden>
                        <td colspan="9">
                            <div class="invoice-search-empty">
                                <i class="bi bi-search"></i>
                                <p>No proposals match your search.</p>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="invoiceSearchClearEmpty">Clear search</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php include __DIR__ . '/partials/modals.php'; ?>

        </main>
    </div>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/admin-translate.js"></script>
<script src="assets/admin-upload.js"></script>
<script src="assets/admin-password.js"></script>
<script>
(function () {
    const openModal = <?= json_encode($openServiceModal ?? false) ?>;
    if (openModal) {
        const el = document.getElementById('modalService');
        if (el) new bootstrap.Modal(el).show();
    }
    <?php if ($section === 'categories'): ?>
    const catModal = document.getElementById('modalCategories');
    if (catModal) new bootstrap.Modal(catModal).show();
    <?php endif; ?>

    document.querySelectorAll('.btn-delete-service').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteServiceId').value = btn.dataset.id;
            document.getElementById('deleteServiceName').textContent = btn.dataset.name;
            new bootstrap.Modal(document.getElementById('modalDeleteService')).show();
        });
    });

    document.querySelectorAll('.btn-delete-invoice').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteInvoiceId').value = btn.dataset.id;
            document.getElementById('deleteInvoiceName').textContent = btn.dataset.name;
            new bootstrap.Modal(document.getElementById('modalDeleteInvoice')).show();
        });
    });

    // Clean URL when closing service modal (remove ?edit= / ?add=)
    const serviceModal = document.getElementById('modalService');
    if (serviceModal) {
        serviceModal.addEventListener('hidden.bs.modal', function () {
            if (location.search.match(/[?&](edit|add)=/)) {
                history.replaceState(null, '', '?section=services');
            }
        });
    }
})();
</script>
<?php if ($authenticated && $section === 'invoices'): ?>
<script src="assets/admin-notify.js?v=<?= (int) @filemtime(__DIR__ . '/assets/admin-notify.js') ?>"></script>
<script>window.INVOICE_LIST_BOOT = <?= json_encode($openInvoiceBoot) ?>;</script>
<script src="assets/invoices-list.js?v=<?= (int) @filemtime(__DIR__ . '/assets/invoices-list.js') ?>"></script>
<?php endif; ?>
<?php if ($authenticated): ?>
    <script src="assets/admin.js?v=<?= (int) @filemtime(__DIR__ . '/assets/admin.js') ?>"></script>
<?php endif; ?>
</body>
</html>
