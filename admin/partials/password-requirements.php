<?php
/** Live password requirement checklist — pair with .js-admin-password-form */
?>
<ul class="admin-password-rules" aria-live="polite">
    <?php foreach (adminPasswordRequirementLabels() as $ruleId => $label): ?>
    <li class="admin-password-rule" data-rule="<?= htmlspecialchars($ruleId) ?>">
        <i class="bi bi-circle rule-icon" aria-hidden="true"></i>
        <span><?= htmlspecialchars($label) ?></span>
    </li>
    <?php endforeach; ?>
</ul>
