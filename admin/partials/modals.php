<?php
/** Admin modal forms — included from index.php when authenticated */
$ab = $aboutPage['sections'] ?? [];
$sp = $servicesPage['sections'] ?? [];
$editService = $editService ?? [];
$d = $editService['detail'] ?? [];
$isServiceEdit = !empty($editService['id']);
$serviceCategories = $serviceCategories ?? cmsGetDefaultServiceCategories();
$categoriesText = implode("\n", $serviceCategories);
?>

<!-- Maintenance -->
<div class="modal fade" id="modalMaintenance" tabindex="-1" aria-labelledby="modalMaintenanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand" id="modalMaintenanceLabel">Maintenance settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="maintenance">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="maintenance_enabled" id="maint_on" <?= $maintenance_enabled ? 'checked' : '' ?>>
                        <label class="form-check-label" for="maint_on">Enable maintenance page for visitors</label>
                    </div>
                    <div class="mb-0">
                        <label>Message (EN)</label>
                        <textarea name="maintenance_message" class="form-control"><?= htmlspecialchars($maintenance_message) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?= url('maintenance.php') ?>" class="btn btn-outline-secondary btn-sm" target="_blank">Preview</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contact -->
<div class="modal fade" id="modalContact" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Contact information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="contact">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Email</label><input name="site_email" class="form-control" value="<?= htmlspecialchars($contactSettings['site_email'] ?? CONTACT_EMAIL) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Phone</label><input name="site_phone" class="form-control" value="<?= htmlspecialchars($contactSettings['site_phone'] ?? CONTACT_PHONE) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Company name (EN)</label><input name="site_name_en" class="form-control" value="<?= htmlspecialchars($contactSettings['site_name_en'] ?? SITE_NAME) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Company name (AR)</label><input name="site_name_ar" class="form-control" value="<?= htmlspecialchars($contactSettings['site_name_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Address (EN)</label><input name="site_address_en" class="form-control" value="<?= htmlspecialchars($contactSettings['site_address_en'] ?? CONTACT_ADDRESS_EN) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Address (AR)</label><input name="site_address_ar" class="form-control" value="<?= htmlspecialchars($contactSettings['site_address_ar'] ?? CONTACT_ADDRESS_AR) ?>"></div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap">
                    <span class="modal-translate-hint"><i class="bi bi-info-circle"></i> Type in EN fields — AR updates automatically</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-translate-all><i class="bi bi-translate"></i> Translate all</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save contact</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Social -->
<div class="modal fade" id="modalSocial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Social & ISO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="social">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Facebook</label><input name="facebook_url" class="form-control" value="<?= htmlspecialchars($contactSettings['facebook_url'] ?? SOCIAL_FACEBOOK) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Twitter</label><input name="twitter_url" class="form-control" value="<?= htmlspecialchars($contactSettings['twitter_url'] ?? SOCIAL_TWITTER) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Instagram</label><input name="instagram_url" class="form-control" value="<?= htmlspecialchars($contactSettings['instagram_url'] ?? SOCIAL_INSTAGRAM) ?>"></div>
                        <div class="col-md-6 mb-3"><label>LinkedIn</label><input name="linkedin_url" class="form-control" value="<?= htmlspecialchars($contactSettings['linkedin_url'] ?? SOCIAL_LINKEDIN) ?>"></div>
                        <div class="col-md-6 mb-3"><label>ISO certificate #</label><input name="iso_certificate" class="form-control" value="<?= htmlspecialchars($contactSettings['iso_certificate'] ?? ISO_CERTIFICATE_NUMBER) ?>"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save social</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- About -->
<div class="modal fade" id="modalAbout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Edit about page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="about">
                    <h6 class="text-muted">Hero</h6>
                    <div class="row mb-3">
                        <div class="col-md-6"><label>Title AR</label><input name="hero_title_ar" class="form-control" value="<?= htmlspecialchars($ab['hero']['title_ar'] ?? 'من نحن') ?>"></div>
                        <div class="col-md-6"><label>Title EN</label><input name="hero_title_en" class="form-control" value="<?= htmlspecialchars($ab['hero']['title_en'] ?? 'About Us') ?>"></div>
                    </div>
                    <h6 class="text-muted">Overview</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Lead AR</label><textarea name="overview_lead_ar" class="form-control"><?= htmlspecialchars($ab['overview']['lead_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Lead EN</label><textarea name="overview_lead_en" class="form-control"><?= htmlspecialchars($ab['overview']['lead_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Paragraph AR</label><textarea name="overview_text_ar" class="form-control"><?= htmlspecialchars($ab['overview']['text_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Paragraph EN</label><textarea name="overview_text_en" class="form-control"><?= htmlspecialchars($ab['overview']['text_en'] ?? '') ?></textarea></div>
                    </div>
                    <h6 class="text-muted">Mission / Vision / Values</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Mission AR</label><textarea name="mission_text_ar" class="form-control"><?= htmlspecialchars($ab['mission']['text_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Mission EN</label><textarea name="mission_text_en" class="form-control"><?= htmlspecialchars($ab['mission']['text_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Vision AR</label><textarea name="vision_text_ar" class="form-control"><?= htmlspecialchars($ab['vision']['text_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Vision EN</label><textarea name="vision_text_en" class="form-control"><?= htmlspecialchars($ab['vision']['text_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Values AR</label><textarea name="values_text_ar" class="form-control"><?= htmlspecialchars($ab['values']['text_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Values EN</label><textarea name="values_text_en" class="form-control"><?= htmlspecialchars($ab['values']['text_en'] ?? '') ?></textarea></div>
                    </div>
                    <h6 class="text-muted">Our story</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Story title AR</label><input name="story_title_ar" class="form-control" value="<?= htmlspecialchars($ab['story']['title_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Story title EN</label><input name="story_title_en" class="form-control" value="<?= htmlspecialchars($ab['story']['title_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Story lead AR</label><textarea name="story_lead_ar" class="form-control"><?= htmlspecialchars($ab['story']['lead_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Story lead EN</label><textarea name="story_lead_en" class="form-control"><?= htmlspecialchars($ab['story']['lead_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Story paragraph 2 AR</label><textarea name="story_text_ar" class="form-control"><?= htmlspecialchars($ab['story']['text_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Story paragraph 2 EN</label><textarea name="story_text_en" class="form-control"><?= htmlspecialchars($ab['story']['text_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Story paragraph 3 AR</label><textarea name="story_text2_ar" class="form-control"><?= htmlspecialchars($ab['story']['text2_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Story paragraph 3 EN</label><textarea name="story_text2_en" class="form-control"><?= htmlspecialchars($ab['story']['text2_en'] ?? '') ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap">
                    <span class="modal-translate-hint"><i class="bi bi-info-circle"></i> Type in EN fields — AR updates automatically</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-translate-all><i class="bi bi-translate"></i> Translate all</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save about page</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Services page -->
<div class="modal fade" id="modalServicesPage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Services listing page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="services_page">
                    <h6 class="text-muted">Hero</h6>
                    <div class="row mb-3">
                        <div class="col-md-6"><label>Hero title AR</label><input name="hero_title_ar" class="form-control" value="<?= htmlspecialchars($sp['hero']['title_ar'] ?? 'خدماتنا') ?>"></div>
                        <div class="col-md-6"><label>Hero title EN</label><input name="hero_title_en" class="form-control" value="<?= htmlspecialchars($sp['hero']['title_en'] ?? 'Our Services') ?>"></div>
                        <div class="col-md-6"><label>Hero subtitle AR</label><textarea name="hero_subtitle_ar" class="form-control"><?= htmlspecialchars($sp['hero']['subtitle_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6"><label>Hero subtitle EN</label><textarea name="hero_subtitle_en" class="form-control"><?= htmlspecialchars($sp['hero']['subtitle_en'] ?? '') ?></textarea></div>
                    </div>
                    <h6 class="text-muted">Intro</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Intro badge AR</label><input name="intro_badge_ar" class="form-control" value="<?= htmlspecialchars($sp['intro']['badge_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Intro badge EN</label><input name="intro_badge_en" class="form-control" value="<?= htmlspecialchars($sp['intro']['badge_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Intro title AR</label><input name="intro_title_ar" class="form-control" value="<?= htmlspecialchars($sp['intro']['title_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Intro title EN</label><input name="intro_title_en" class="form-control" value="<?= htmlspecialchars($sp['intro']['title_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Intro lead AR</label><textarea name="intro_lead_ar" class="form-control"><?= htmlspecialchars($sp['intro']['lead_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Intro lead EN</label><textarea name="intro_lead_en" class="form-control"><?= htmlspecialchars($sp['intro']['lead_en'] ?? '') ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap">
                    <span class="modal-translate-hint"><i class="bi bi-info-circle"></i> Type in EN fields — AR updates automatically</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-translate-all><i class="bi bi-translate"></i> Translate all</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Service add/edit -->
<div class="modal fade" id="modalService" tabindex="-1" aria-hidden="true" data-auto-open="<?= (!empty($_GET['edit']) || !empty($_GET['add'])) ? '1' : '0' ?>">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content">
            <form method="post" action="actions.php" id="formService">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand"><?= $isServiceEdit ? 'Edit service' : 'Add new service' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="service_save">
                    <?php if ($isServiceEdit): ?><input type="hidden" name="id" value="<?= (int)$editService['id'] ?>"><?php endif; ?>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label>Slug (URL)</label><input name="slug" class="form-control" required value="<?= htmlspecialchars($editService['slug'] ?? '') ?>" placeholder="household-management" <?= $isServiceEdit ? 'readonly' : '' ?>></div>
                        <div class="col-md-4 mb-3">
                            <label>Category <a href="?section=categories" class="small fw-normal">(manage list)</a></label>
                            <input name="category" class="form-control" list="serviceCategoryList" required
                                value="<?= htmlspecialchars($editService['category'] ?? '') ?>"
                                placeholder="e.g. household or my-new-category">
                            <datalist id="serviceCategoryList">
                                <?php foreach ($serviceCategories as $c): ?>
                                <option value="<?= htmlspecialchars($c) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                            <p class="hint mb-0">Pick from the list or type a new slug. Add permanent options under <strong>Categories</strong> in the menu.</p>
                        </div>
                        <div class="col-md-4 mb-3"><label>Display order</label><input type="number" name="display_order" class="form-control" value="<?= (int)($editService['display_order'] ?? 0) ?>"></div>
                        <div class="col-md-6 mb-3"><label>Title AR</label><input name="title_ar" class="form-control" value="<?= htmlspecialchars($editService['title_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Title EN</label><input name="title_en" class="form-control" value="<?= htmlspecialchars($editService['title_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Short description AR</label><textarea name="description_ar" class="form-control"><?= htmlspecialchars($editService['description_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Short description EN</label><textarea name="description_en" class="form-control"><?= htmlspecialchars($editService['description_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Icon</label><input name="icon" class="form-control" value="<?= htmlspecialchars($editService['icon'] ?? 'bi-star') ?>" placeholder="bi-house-door"></div>
                        <div class="col-12 mb-3">
                            <label>Service image</label>
                            <div class="service-image-upload border rounded p-3 bg-white">
                                <div class="d-flex flex-wrap gap-3 align-items-start">
                                    <div id="serviceImagePreviewWrap" class="service-image-preview-wrap" <?= empty($editService['image']) ? 'hidden' : '' ?>>
                                        <img id="serviceImagePreview" src="<?= !empty($editService['image']) ? htmlspecialchars(url($editService['image'])) : '' ?>" alt="" class="rounded border" style="max-width:200px;max-height:160px;object-fit:cover;" <?= empty($editService['image']) ? 'hidden' : '' ?>>
                                    </div>
                                    <div class="flex-grow-1" style="min-width:200px">
                                        <input type="file" id="serviceImageFile" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png,image/webp,image/gif">
                                        <input type="hidden" name="image" id="serviceImagePath" value="<?= htmlspecialchars($editService['image'] ?? '') ?>">
                                        <p class="hint mb-1">JPG, PNG, WebP or GIF — max 5 MB. Choose a file and it uploads automatically.</p>
                                        <p id="serviceImageUploadStatus" class="hint mt-1 service-upload-status"></p>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" id="serviceImageClear" <?= empty($editService['image']) ? 'hidden' : '' ?>>Remove image</button>
                                        <details class="mt-2">
                                            <summary class="small text-muted" style="cursor:pointer">Advanced: paste image path</summary>
                                            <input type="text" class="form-control form-control-sm mt-1" id="serviceImagePathManual" placeholder="assets/images/services/my-photo.jpg" value="<?= htmlspecialchars($editService['image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm btn-link px-0" id="serviceImagePathApply">Apply path</button>
                                        </details>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3"><label>Listing features AR (one per line)</label><textarea name="listing_features_ar" class="form-control"><?= htmlspecialchars($editService['listing_features_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Listing features EN (one per line)</label><textarea name="listing_features_en" class="form-control"><?= htmlspecialchars($editService['listing_features_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Detail subtitle AR</label><input name="subtitle_ar" class="form-control" value="<?= htmlspecialchars($d['subtitle_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Detail subtitle EN</label><input name="subtitle_en" class="form-control" value="<?= htmlspecialchars($d['subtitle_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Overview title AR</label><input name="overview_title_ar" class="form-control" value="<?= htmlspecialchars($d['overview_title_ar'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Overview title EN</label><input name="overview_title_en" class="form-control" value="<?= htmlspecialchars($d['overview_title_en'] ?? '') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Overview paragraph 1 AR</label><textarea name="overview_p1_ar" class="form-control"><?= htmlspecialchars($d['overview_p1_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Overview paragraph 1 EN</label><textarea name="overview_p1_en" class="form-control"><?= htmlspecialchars($d['overview_p1_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Overview paragraph 2 AR</label><textarea name="overview_p2_ar" class="form-control"><?= htmlspecialchars($d['overview_p2_ar'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Overview paragraph 2 EN</label><textarea name="overview_p2_en" class="form-control"><?= htmlspecialchars($d['overview_p2_en'] ?? '') ?></textarea></div>
                        <div class="col-md-6 mb-3"><label>Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= ($editService['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($editService['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="featured" id="feat" <?= !empty($editService['featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="feat">Featured</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap">
                    <span class="modal-translate-hint"><i class="bi bi-info-circle"></i> Type in EN fields — AR updates automatically</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-translate-all><i class="bi bi-translate"></i> Translate all</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Service categories -->
<div class="modal fade" id="modalCategories" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <form method="post" action="actions.php">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Service categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="categories_save">
                    <p class="small text-muted">One category per line. Use lowercase slugs (letters, numbers, hyphens). Example: <code>luxury-travel</code></p>
                    <label>Category list</label>
                    <textarea name="categories" class="form-control font-monospace" rows="12" placeholder="household&#10;events&#10;protocol"><?= htmlspecialchars($categoriesText) ?></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save categories</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete service confirmation -->
<div class="modal fade" id="modalDeleteService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="actions.php" id="formDeleteService">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Delete service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="service_delete">
                    <input type="hidden" name="id" id="deleteServiceId" value="">
                    <p class="mb-0">Delete <strong id="deleteServiceName"></strong>? This cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete invoice confirmation -->
<div class="modal fade" id="modalDeleteInvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="actions.php" id="formDeleteInvoice">
                <input type="hidden" name="admin_csrf" value="<?= htmlspecialchars(adminCsrfToken()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title brand">Delete invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="section" value="invoice_delete">
                    <input type="hidden" name="id" id="deleteInvoiceId" value="">
                    <p class="mb-0">Delete <strong id="deleteInvoiceName"></strong>? This cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/modal-invoice.php'; ?>
