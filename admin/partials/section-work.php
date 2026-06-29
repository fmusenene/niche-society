<?php
/** Work documents editor — expects $workDocuments list */
$workDocuments = $workDocuments ?? [];
$editWorkId = !empty($_GET['edit']) ? (int) $_GET['edit'] : 0;
?>
<div class="work-page">
    <div class="card-admin work-list-card">
        <div class="work-list-toolbar">
            <p class="work-list-intro mb-0">Type and format your text, then print or save as PDF with the same proposal header, watermark, and footer.</p>
            <button type="button" class="btn btn-primary btn-sm" id="btnNewWork">
                <i class="bi bi-plus-lg"></i> New document
            </button>
        </div>

        <?php if (count($workDocuments) === 0): ?>
        <div class="work-empty-state">
            <div class="work-empty-state__icon"><i class="bi bi-journal-text"></i></div>
            <h3 class="work-empty-state__title">No work documents yet</h3>
            <p class="work-empty-state__text">Create a document, format your content, then print or save as PDF.</p>
            <button type="button" class="btn btn-primary btn-sm" id="btnNewWorkEmpty">
                <i class="bi bi-plus-lg"></i> Create first document
            </button>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table work-data-table mb-0">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Language</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($workDocuments as $row): ?>
                    <?php $snippet = cmsWorkBodyPlainSnippet((string) ($row['body'] ?? '')); ?>
                    <tr>
                        <td><?= htmlspecialchars($snippet) ?></td>
                        <td><?= ($row['language'] ?? 'en') === 'ar' ? 'Arabic' : 'English' ?></td>
                        <td><?= htmlspecialchars($row['updated_at'] ?? '') ?></td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-work" data-id="<?= (int) $row['id'] ?>">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-print-work" data-id="<?= (int) $row['id'] ?>">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-work" data-id="<?= (int) $row['id'] ?>" data-title="<?= htmlspecialchars($snippet, ENT_QUOTES) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalWorkForm" tabindex="-1" aria-labelledby="modalWorkFormTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content work-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWorkFormTitle">Work document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formWork" class="modal-body">
                <input type="hidden" id="workFormId" value="">
                <div class="work-editor-wrap">
                    <div class="work-editor-toolbar" role="toolbar" aria-label="Text formatting">
                        <div class="work-editor-toolbar__group">
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="strikeThrough" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
                        </div>
                        <div class="work-editor-toolbar__group">
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="justifyLeft" title="Align left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="justifyCenter" title="Align center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="justifyRight" title="Align right"><i class="bi bi-text-right"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="justifyFull" title="Justify"><i class="bi bi-justify"></i></button>
                        </div>
                        <div class="work-editor-toolbar__group">
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="insertUnorderedList" title="Bullet list"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary work-cmd-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="bi bi-list-ol"></i></button>
                        </div>
                        <div class="work-editor-toolbar__group">
                            <select class="form-select form-select-sm work-heading-select" id="workHeadingSelect" aria-label="Text style">
                                <option value="p">Normal</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                            </select>
                        </div>
                        <div class="work-editor-toolbar__group work-editor-toolbar__group--translate">
                            <button type="button" class="btn btn-sm btn-outline-primary work-translate-btn" id="btnWorkTranslateEn" data-target="en" title="Translate content to English">
                                <i class="bi bi-translate"></i> To EN
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary work-translate-btn" id="btnWorkTranslateAr" data-target="ar" title="Translate content to Arabic">
                                <i class="bi bi-translate"></i> To AR
                            </button>
                        </div>
                        <div class="work-editor-toolbar__group work-editor-toolbar__group--lang">
                            <span class="small text-muted me-1">Language</span>
                            <div class="btn-group work-language-pills" role="group">
                                <input class="btn-check" type="radio" name="workFormLanguage" id="workLangEn" value="en" checked>
                                <label class="btn btn-outline-secondary btn-sm" for="workLangEn">EN</label>
                                <input class="btn-check" type="radio" name="workFormLanguage" id="workLangAr" value="ar">
                                <label class="btn btn-outline-secondary btn-sm" for="workLangAr">AR</label>
                            </div>
                        </div>
                    </div>
                    <div id="workFormBody" class="work-editor-body" contenteditable="true" dir="ltr" aria-label="Document content" data-placeholder="Start typing here…"></div>
                </div>
                <p class="small text-muted mt-2 mb-0">Use the toolbar for formatting. <strong>To EN</strong> / <strong>To AR</strong> translate your content with Google Translate while keeping headings and lists. Print keeps the proposal header, watermark, and footer.</p>
                <p id="workFormError" class="alert alert-danger d-none mt-3 mb-0" role="alert"></p>
            </form>
            <div class="modal-footer work-modal__footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary" id="btnWorkPrint" disabled>
                    <i class="bi bi-printer"></i> Print / PDF
                </button>
                <button type="submit" form="formWork" class="btn btn-primary" id="btnWorkSave" disabled>
                    <i class="bi bi-check-lg"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteWork" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Delete <strong id="deleteWorkTitle"></strong>? This cannot be undone.</p>
                <input type="hidden" id="deleteWorkId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDeleteWork">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>window.WORK_BOOT = <?= json_encode(['editId' => $editWorkId]) ?>;</script>
