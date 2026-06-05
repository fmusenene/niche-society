<?php
/** Sidebar navigation — expects $section, SITE_NAME, SITE_URL */
$navItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
    ['id' => 'maintenance', 'label' => 'Maintenance', 'icon' => 'bi-tools'],
    ['id' => 'services', 'label' => 'Services', 'icon' => 'bi-grid'],
    ['id' => 'categories', 'label' => 'Categories', 'icon' => 'bi-tags'],
    ['id' => 'services_page', 'label' => 'Services page', 'icon' => 'bi-file-earmark-text'],
    ['id' => 'about', 'label' => 'About', 'icon' => 'bi-info-circle'],
    ['id' => 'contact', 'label' => 'Contact', 'icon' => 'bi-envelope'],
    ['id' => 'account', 'label' => 'Account', 'icon' => 'bi-key'],
];
?>
<div class="admin-sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <h1><i class="bi bi-shield-lock me-1"></i> Site Admin</h1>
        <p><?= htmlspecialchars(SITE_NAME) ?></p>
    </div>
    <nav class="admin-sidebar-nav" aria-label="Admin navigation">
        <p class="admin-sidebar-label">Content</p>
        <?php foreach ($navItems as $item): ?>
        <a href="?section=<?= htmlspecialchars($item['id']) ?>"
           class="admin-sidebar-link <?= $section === $item['id'] ? 'active' : '' ?>">
            <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
            <?= htmlspecialchars($item['label']) ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-footer">
        <a href="<?= htmlspecialchars(rtrim(SITE_URL, '/')) ?>" class="admin-sidebar-link" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i> View website
        </a>
        <form method="post" class="mb-0">
            <button type="submit" name="logout" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
