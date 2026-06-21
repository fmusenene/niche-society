<?php
/**
 * Event proposal / invoice storage
 */

function cmsEnsureInvoicesTable(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(30) NULL DEFAULT NULL,
        record_type ENUM('proposal', 'invoice') NOT NULL DEFAULT 'proposal',
        source_proposal_id INT UNSIGNED NULL DEFAULT NULL,
        subject VARCHAR(255) NOT NULL DEFAULT '',
        offer_date VARCHAR(50) NOT NULL DEFAULT '',
        due_date VARCHAR(50) NOT NULL DEFAULT '',
        event_date VARCHAR(50) NOT NULL DEFAULT '',
        client_name VARCHAR(255) NOT NULL DEFAULT '',
        client_email VARCHAR(255) NOT NULL DEFAULT '',
        client_phone VARCHAR(100) NOT NULL DEFAULT '',
        client_address TEXT NULL,
        prepared_by VARCHAR(255) NOT NULL DEFAULT '',
        currency VARCHAR(3) NOT NULL DEFAULT 'SAR',
        grand_total INT NOT NULL DEFAULT 0,
        status ENUM('draft', 'sent', 'signed', 'cancelled') NOT NULL DEFAULT 'draft',
        data_json LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_invoice_number (invoice_number),
        INDEX idx_subject (subject),
        INDEX idx_event_date (event_date),
        INDEX idx_status (status),
        INDEX idx_record_type (record_type),
        INDEX idx_source_proposal (source_proposal_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    cmsMigrateInvoicesTable($pdo);
}

function cmsMigrateInvoicesTable(PDO $pdo): void
{
    if (!cmsTableExists($pdo, 'invoices')) {
        return;
    }
    if (!cmsColumnExists($pdo, 'invoices', 'invoice_number')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN invoice_number VARCHAR(30) NOT NULL DEFAULT '' AFTER id");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'due_date')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN due_date VARCHAR(50) NOT NULL DEFAULT '' AFTER offer_date");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'client_email')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN client_email VARCHAR(255) NOT NULL DEFAULT '' AFTER client_name");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'client_phone')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN client_phone VARCHAR(100) NOT NULL DEFAULT '' AFTER client_email");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'client_address')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN client_address TEXT NULL AFTER client_phone");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'record_type')) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN record_type ENUM('proposal', 'invoice') NOT NULL DEFAULT 'proposal' AFTER invoice_number");
        $pdo->exec("UPDATE invoices SET record_type = 'invoice' WHERE invoice_number IS NOT NULL AND invoice_number != ''");
        $pdo->exec("UPDATE invoices SET record_type = 'proposal', invoice_number = NULL WHERE invoice_number IS NULL OR invoice_number = ''");
    }
    if (!cmsColumnExists($pdo, 'invoices', 'source_proposal_id')) {
        $pdo->exec('ALTER TABLE invoices ADD COLUMN source_proposal_id INT UNSIGNED NULL DEFAULT NULL AFTER record_type');
        try {
            $pdo->exec('ALTER TABLE invoices ADD INDEX idx_source_proposal (source_proposal_id)');
        } catch (Throwable $e) {
            /* index may already exist */
        }
    }

    try {
        $pdo->exec('ALTER TABLE invoices MODIFY invoice_number VARCHAR(30) NULL DEFAULT NULL');
    } catch (Throwable $e) {
        /* column may already allow NULL */
    }

    $stmt = $pdo->query("SHOW INDEX FROM invoices WHERE Key_name = 'uniq_invoice_number'");
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        try {
            $pdo->exec('ALTER TABLE invoices ADD UNIQUE KEY uniq_invoice_number (invoice_number)');
        } catch (Throwable $e) {
            /* index may already exist under another name */
        }
    }
}

function cmsInvoiceRecordType(array $row): string
{
    $type = strtolower(trim((string) ($row['record_type'] ?? 'invoice')));

    return $type === 'proposal' ? 'proposal' : 'invoice';
}

function cmsInvoiceIsProposal(array $row): bool
{
    return cmsInvoiceRecordType($row) === 'proposal';
}

function cmsInvoiceTypeLabel(array $row): string
{
    return cmsInvoiceIsProposal($row) ? 'Proposal' : 'Invoice';
}

function cmsGenerateInvoiceNumber(PDO $pdo): string
{
    $year = date('Y');
    $prefix = 'NS-' . $year . '-';

    for ($attempt = 0; $attempt < 20; $attempt++) {
        $stmt = $pdo->prepare('SELECT invoice_number FROM invoices WHERE invoice_number LIKE ? ORDER BY invoice_number DESC LIMIT 1');
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();
        $seq = 1;
        if (is_string($last) && preg_match('/-(\d+)$/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        $candidate = $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);

        $check = $pdo->prepare('SELECT id FROM invoices WHERE invoice_number = ? LIMIT 1');
        $check->execute([$candidate]);
        if (!$check->fetch(PDO::FETCH_ASSOC)) {
            return $candidate;
        }
    }

    return $prefix . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
}

function cmsInvoiceDefaultCategories(): array
{
    return [
        [
            'name' => '',
            'items' => [
                ['description' => '', 'price' => 0, 'quantity' => 1, 'details' => []],
            ],
        ],
    ];
}

function cmsInvoiceDefaultProposalFields(string $lang = 'en'): array
{
    if ($lang === 'ar') {
        return [
            'intro1' => 'يسعدنا أن تتاح لنا الفرصة لتقديم عرضنا لإدارة فعاليتكم القادمة.',
            'intro2' => 'نقدم لكم ملخصًا كاملاً للعرض الفني والمالي الذي توفره نيش سوسايتي.',
            'intro3' => 'شكرًا لاختياركم نيش سوسايتي كأحد المرشحين للشراكة في فعاليتكم القادمة.',
            'cancellationPolicy' => "الدفعة الأولى غير مستردة.\nلا يتم رد أي دفعة إذا تم الإلغاء قبل أقل من 60 يومًا من الفعالية.\nعروض التصوير والفيديو لها أسعار خاصة مع خدمات تنظيم حفلات الزفاف.",
            'closing1' => 'نأمل أن يكون ما ورد أعلاه مناسبًا لكم ونتطلع إلى تأكيدكم قريبًا،',
            'closing2' => 'يرجى إرسال بريد إلكتروني يفيد بالموافقة وتأكيد هذه الترتيبات.',
            'closing3' => 'لأي استفسار إضافي يرجى التواصل مباشرة على +966 54 694 7915، ويسعدنا تقديم المساعدة.',
            'closingRegards' => 'مع أطيب التحيات،',
            'socialIntro' => 'يمكن للعميل الموافقة أو عدم الموافقة على نشر محتوى الفعالية (صور، فيديو، وتغطية) على المنصات أدناه. ضع علامة على غير موافق إذا لم تكن نيش سوسايتي تنشر محتوى الفعالية على تلك المنصة دون موافقة خطية منفصلة.',
            'socialSnapchat' => '',
            'socialInstagram' => '',
            'socialFacebook' => '',
        ];
    }

    return [
        'intro1' => "It's our pleasure for giving us the opportunity to present our proposal regarding the management of your upcoming event.",
        'intro2' => 'We are writing you to summarize the complete technical and financial proposal that Niche Society has to offer.',
        'intro3' => 'Thank you for supporting Niche Society to be one of the candidates that you considered as a partner for your upcoming event.',
        'cancellationPolicy' => "First payment is non-refundable.\nNo refund of any payment will be done if cancellation will occur less than 60 days before the event.\nPhotography and video offer have special rates with wedding planning services.",
        'closing1' => 'We hope that the above meets your approval and we look forward to receive your confirmation soon,',
        'closing2' => 'Kindly send us an email indicating your approval and confirmation of these arrangements.',
        'closing3' => 'However, should you require any further assistance, please feel free to contact us directly at +966 54 694 7915, we will be more than happy to provide you with any assistance you require.',
        'closingRegards' => 'Best Regards,',
        'socialIntro' => 'The client may authorize or decline publication of event-related content (photos, videos, and coverage) on the platforms below. Tick Not Approved if Niche Society should not publish event-related content on that platform without separate written consent.',
        'socialSnapchat' => '',
        'socialInstagram' => '',
        'socialFacebook' => '',
    ];
}

function cmsInvoiceDefaultPaymentTerms(string $lang = 'en'): string
{
    if ($lang === 'ar') {
        return "الدفعة الأولى: 30% من إجمالي المبلغ عند توقيع العقد\n"
            . "الدفعة الثانية: 40% من إجمالي المبلغ قبل الفعالية\n"
            . "الدفعة الثالثة: 30% من إجمالي المبلغ في يوم الفعالية";
    }

    return "First payment: 30% of total amount is required upon signing the contract\n"
        . "Second payment: 40% of the total amount is required before the event\n"
        . "Third payment: 30% of the total amount is required on the day of the event";
}

function cmsInvoiceDefaultState(): array
{
    return [
        'categories' => cmsInvoiceDefaultCategories(),
        'fields' => array_merge([
            'offerDate' => '',
            'eventDate' => '',
            'location' => '',
            'subject' => '',
            'prepared' => '',
            'currency' => 'SAR',
            'discount' => '0',
            'clientName' => '',
            'clientAddress' => '',
            'clientEmail' => '',
            'clientPhone' => '',
            'signatureDate' => '',
            'dueDate' => '',
            'paymentTerms' => cmsInvoiceDefaultPaymentTerms('en'),
            'notes' => '',
            'language' => 'en',
        ], cmsInvoiceDefaultProposalFields()),
    ];
}

function cmsInvoiceFormatMoney(int $amount, string $currency = 'SAR'): string
{
    return number_format($amount) . ' ' . $currency;
}

/** Normalize stored dates for display (ISO Y-m-d → d-m-Y; other formats unchanged). */
function cmsInvoiceFormatDisplayDate(?string $value, bool $emptyAsDash = false): string
{
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return $emptyAsDash ? '—' : '';
    }
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
        return (int) $m[3] . '-' . (int) $m[2] . '-' . $m[1];
    }

    return $value;
}

/** @return array{subtotal:int,fees:int,discount:int,grand:int,pay1:int,pay2:int,pay3:int,qty_total:int} */
function cmsInvoiceComputeBreakdown(array $categories, float $discountPercent = 0): array
{
    $subtotal = 0;
    $qtyTotal = 0;
    foreach ($categories as $cat) {
        if (!is_array($cat['items'] ?? null)) {
            continue;
        }
        foreach ($cat['items'] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $price = max(0, (int) ($item['price'] ?? 0));
            $qty = max(0, (int) ($item['quantity'] ?? 0));
            $subtotal += $price * $qty;
            $qtyTotal += $qty;
        }
    }

    $discountPercent = max(0, min(100, $discountPercent));
    $fees = (int) round($subtotal * 0.15);
    $discount = (int) round($subtotal * ($discountPercent / 100));
    $grand = max(0, $subtotal + $fees - $discount);

    return [
        'subtotal' => $subtotal,
        'fees' => $fees,
        'discount' => $discount,
        'grand' => $grand,
        'pay1' => (int) round($grand * 0.3),
        'pay2' => (int) round($grand * 0.4),
        'pay3' => (int) round($grand * 0.3),
        'qty_total' => $qtyTotal,
    ];
}

function cmsInvoiceComputeGrandTotal(array $categories, float $discountPercent = 0): int
{
    return cmsInvoiceComputeBreakdown($categories, $discountPercent)['grand'];
}

function cmsInvoiceNormalizeState(array $state): array
{
    if (!isset($state['fields']) && (isset($state['clientName']) || isset($state['offerDate']) || isset($state['dueDate']))) {
        $state = [
            'categories' => $state['categories'] ?? [],
            'fields' => $state,
        ];
    }

    $defaults = cmsInvoiceDefaultState();
    $fields = is_array($state['fields'] ?? null) ? $state['fields'] : [];
    $categories = is_array($state['categories'] ?? null) ? $state['categories'] : [];

    $normalizedCategories = [];
    foreach ($categories as $cat) {
        if (!is_array($cat)) {
            continue;
        }
        $items = [];
        foreach ($cat['items'] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $details = [];
            foreach ($item['details'] ?? [] as $detail) {
                $detail = trim((string) $detail);
                if ($detail !== '') {
                    $details[] = $detail;
                }
            }
            $items[] = [
                'description' => trim((string) ($item['description'] ?? '')),
                'details' => $details,
                'price' => max(0, (int) ($item['price'] ?? 0)),
                'quantity' => max(0, (int) ($item['quantity'] ?? 0)),
            ];
        }
        if ($items === []) {
            continue;
        }
        $normalizedCategories[] = [
            'name' => trim((string) ($cat['name'] ?? '')),
            'items' => $items,
        ];
    }

    if ($normalizedCategories === []) {
        $normalizedCategories = $defaults['categories'];
    }

    $currency = ($fields['currency'] ?? 'SAR') === 'JOD' ? 'JOD' : 'SAR';
    $language = ($fields['language'] ?? 'en') === 'ar' ? 'ar' : 'en';

    $normalizedFields = [
        'offerDate' => trim((string) ($fields['offerDate'] ?? $fields['date'] ?? '')),
        'eventDate' => trim((string) ($fields['eventDate'] ?? '')),
        'location' => trim((string) ($fields['location'] ?? '')),
        'subject' => trim((string) ($fields['subject'] ?? '')),
        'prepared' => trim((string) ($fields['prepared'] ?? '')),
        'currency' => $currency,
        'discount' => (string) max(0, min(100, (int) ($fields['discount'] ?? 0))),
        'clientName' => trim((string) ($fields['clientName'] ?? $fields['client_name'] ?? ''))
            ?: trim((string) ($fields['subject'] ?? '')),
        'clientAddress' => trim((string) ($fields['clientAddress'] ?? $fields['client_address'] ?? '')),
        'clientEmail' => trim((string) ($fields['clientEmail'] ?? $fields['client_email'] ?? '')),
        'clientPhone' => trim((string) ($fields['clientPhone'] ?? $fields['client_phone'] ?? '')),
        'signatureDate' => trim((string) ($fields['signatureDate'] ?? '')),
        'dueDate' => trim((string) ($fields['dueDate'] ?? $fields['due_date'] ?? '')),
        'paymentTerms' => trim((string) ($fields['paymentTerms'] ?? cmsInvoiceDefaultPaymentTerms($language))),
        'notes' => trim((string) ($fields['notes'] ?? '')),
        'language' => $language,
    ];

    $proposalDefaults = cmsInvoiceDefaultProposalFields($language);
    foreach ($proposalDefaults as $key => $defaultVal) {
        if (!array_key_exists($key, $fields)) {
            $normalizedFields[$key] = $defaultVal;
            continue;
        }
        $val = $fields[$key];
        if (str_starts_with($key, 'social')) {
            $normalizedFields[$key] = in_array($val, ['approved', 'not_approved'], true) ? $val : '';
        } else {
            $normalizedFields[$key] = trim((string) $val);
        }
    }

    return [
        'categories' => $normalizedCategories,
        'fields' => $normalizedFields,
    ];
}

function cmsInvoiceMetaFromState(array $state): array
{
    $fields = $state['fields'];
    $discount = (float) ($fields['discount'] ?? 0);

    return [
        'subject' => $fields['subject'] ?: 'Untitled proposal',
        'offer_date' => $fields['offerDate'],
        'due_date' => $fields['dueDate'],
        'event_date' => $fields['eventDate'],
        'client_name' => $fields['clientName'],
        'client_email' => $fields['clientEmail'],
        'client_phone' => $fields['clientPhone'],
        'client_address' => $fields['clientAddress'],
        'prepared_by' => $fields['prepared'],
        'currency' => $fields['currency'],
        'grand_total' => cmsInvoiceComputeGrandTotal($state['categories'], $discount),
    ];
}

function cmsGetAllInvoices(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, invoice_number, record_type, source_proposal_id, subject, offer_date, event_date, client_name, prepared_by, currency, grand_total, status, created_at, updated_at FROM invoices ORDER BY updated_at DESC, id DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/** @return array<int, array{id:int, invoice_number:string}> */
function cmsGetProposalInvoiceMap(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT id, source_proposal_id, invoice_number FROM invoices WHERE record_type = 'invoice' AND source_proposal_id IS NOT NULL");
    $map = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $proposalId = (int) ($row['source_proposal_id'] ?? 0);
        if ($proposalId > 0) {
            $map[$proposalId] = [
                'id' => (int) $row['id'],
                'invoice_number' => (string) ($row['invoice_number'] ?? ''),
            ];
        }
    }

    return $map;
}

function cmsGetLinkedInvoiceForProposal(PDO $pdo, int $proposalId): ?array
{
    if ($proposalId <= 0) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id FROM invoices WHERE source_proposal_id = ? AND record_type = 'invoice' ORDER BY id DESC LIMIT 1");
    $stmt->execute([$proposalId]);
    $invoiceId = (int) ($stmt->fetchColumn() ?: 0);
    if ($invoiceId <= 0) {
        return null;
    }

    return cmsGetInvoice($pdo, $invoiceId);
}

function cmsInvoiceLinkedMeta(PDO $pdo, array $row): array
{
    if (!cmsInvoiceIsProposal($row)) {
        return ['linked_invoice_id' => 0, 'linked_invoice_number' => ''];
    }
    $linked = cmsGetLinkedInvoiceForProposal($pdo, (int) ($row['id'] ?? 0));
    if (!$linked) {
        return ['linked_invoice_id' => 0, 'linked_invoice_number' => ''];
    }

    return [
        'linked_invoice_id' => (int) $linked['id'],
        'linked_invoice_number' => (string) ($linked['invoice_number'] ?? ''),
    ];
}

function cmsGetInvoice(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return null;
    }
    $decoded = json_decode($row['data_json'] ?? '{}', true);
    $state = is_array($decoded) ? cmsInvoiceNormalizeState($decoded) : cmsInvoiceDefaultState();
    $row['state'] = cmsInvoiceEnrichStateFromRow($row, $state);
    return $row;
}

function cmsInvoiceEnrichStateFromRow(array $row, array $state): array
{
    $fields = $state['fields'] ?? [];
    $map = [
        'dueDate' => 'due_date',
        'clientAddress' => 'client_address',
        'clientEmail' => 'client_email',
        'clientPhone' => 'client_phone',
        'clientName' => 'client_name',
    ];
    foreach ($map as $fieldKey => $column) {
        if (trim((string) ($fields[$fieldKey] ?? '')) === '') {
            $fields[$fieldKey] = trim((string) ($row[$column] ?? ''));
        }
    }
    $state['fields'] = $fields;
    return $state;
}

function cmsCreateProposal(PDO $pdo, ?array $state = null): int
{
    $state = cmsInvoiceNormalizeState($state ?? cmsInvoiceDefaultState());
    $meta = cmsInvoiceMetaFromState($state);
    $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $stmt = $pdo->prepare('INSERT INTO invoices (invoice_number, record_type, subject, offer_date, due_date, event_date, client_name, client_email, client_phone, client_address, prepared_by, currency, grand_total, status, data_json)
        VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        'proposal',
        $meta['subject'],
        $meta['offer_date'],
        $meta['due_date'],
        $meta['event_date'],
        $meta['client_name'],
        $meta['client_email'],
        $meta['client_phone'],
        $meta['client_address'],
        $meta['prepared_by'],
        $meta['currency'],
        $meta['grand_total'],
        'draft',
        $json,
    ]);

    return (int) $pdo->lastInsertId();
}

/** @deprecated Use cmsCreateProposal() */
function cmsCreateInvoice(PDO $pdo, ?array $state = null): int
{
    return cmsCreateProposal($pdo, $state);
}

function cmsConvertProposalToInvoice(PDO $pdo, int $proposalId, ?array $state = null): array
{
    $proposal = cmsGetInvoice($pdo, $proposalId);
    if (!$proposal) {
        throw new RuntimeException('Proposal not found.');
    }
    if (!cmsInvoiceIsProposal($proposal)) {
        throw new RuntimeException('This record is already an invoice.');
    }

    $existing = cmsGetLinkedInvoiceForProposal($pdo, $proposalId);
    if ($existing) {
        return $existing;
    }

    if ($state !== null) {
        cmsSaveInvoice($pdo, $proposalId, $state);
        $proposal = cmsGetInvoice($pdo, $proposalId);
        if (!$proposal) {
            throw new RuntimeException('Proposal not found after save.');
        }
    }

    $invoiceState = $proposal['state'];
    $meta = cmsInvoiceMetaFromState($invoiceState);
    $json = json_encode($invoiceState, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $invoiceNumber = cmsGenerateInvoiceNumber($pdo);

    $stmt = $pdo->prepare('INSERT INTO invoices (invoice_number, record_type, source_proposal_id, subject, offer_date, due_date, event_date, client_name, client_email, client_phone, client_address, prepared_by, currency, grand_total, status, data_json)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $invoiceNumber,
        'invoice',
        $proposalId,
        $meta['subject'],
        $meta['offer_date'],
        $meta['due_date'],
        $meta['event_date'],
        $meta['client_name'],
        $meta['client_email'],
        $meta['client_phone'],
        $meta['client_address'],
        $meta['prepared_by'],
        $meta['currency'],
        $meta['grand_total'],
        'draft',
        $json,
    ]);

    $invoiceId = (int) $pdo->lastInsertId();
    $invoice = cmsGetInvoice($pdo, $invoiceId);
    if (!$invoice) {
        throw new RuntimeException('Could not load new invoice.');
    }

    return $invoice;
}

function cmsSyncInvoiceFromProposal(PDO $pdo, int $proposalId, ?array $state = null): array
{
    $proposal = cmsGetInvoice($pdo, $proposalId);
    if (!$proposal || !cmsInvoiceIsProposal($proposal)) {
        throw new RuntimeException('Proposal not found.');
    }

    $invoice = cmsGetLinkedInvoiceForProposal($pdo, $proposalId);
    if (!$invoice) {
        throw new RuntimeException('No invoice exists for this proposal yet. Use Make invoice first.');
    }

    if ($state !== null) {
        cmsSaveInvoice($pdo, $proposalId, $state);
        $proposal = cmsGetInvoice($pdo, $proposalId);
        if (!$proposal) {
            throw new RuntimeException('Proposal not found after save.');
        }
    }

    cmsSaveInvoice($pdo, (int) $invoice['id'], $proposal['state']);
    $updated = cmsGetInvoice($pdo, (int) $invoice['id']);
    if (!$updated) {
        throw new RuntimeException('Could not update linked invoice.');
    }

    return $updated;
}

function cmsSaveInvoice(PDO $pdo, int $id, array $state, ?string $status = null): void
{
    $state = cmsInvoiceNormalizeState($state);
    $meta = cmsInvoiceMetaFromState($state);
    $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $allowedStatuses = ['draft', 'sent', 'signed', 'cancelled'];
    if ($status !== null && in_array($status, $allowedStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE invoices SET subject = ?, offer_date = ?, due_date = ?, event_date = ?, client_name = ?, client_email = ?, client_phone = ?, client_address = ?, prepared_by = ?, currency = ?, grand_total = ?, status = ?, data_json = ? WHERE id = ?');
        $stmt->execute([
            $meta['subject'],
            $meta['offer_date'],
            $meta['due_date'],
            $meta['event_date'],
            $meta['client_name'],
            $meta['client_email'],
            $meta['client_phone'],
            $meta['client_address'],
            $meta['prepared_by'],
            $meta['currency'],
            $meta['grand_total'],
            $status,
            $json,
            $id,
        ]);
        return;
    }

    $stmt = $pdo->prepare('UPDATE invoices SET subject = ?, offer_date = ?, due_date = ?, event_date = ?, client_name = ?, client_email = ?, client_phone = ?, client_address = ?, prepared_by = ?, currency = ?, grand_total = ?, data_json = ? WHERE id = ?');
    $stmt->execute([
        $meta['subject'],
        $meta['offer_date'],
        $meta['due_date'],
        $meta['event_date'],
        $meta['client_name'],
        $meta['client_email'],
        $meta['client_phone'],
        $meta['client_address'],
        $meta['prepared_by'],
        $meta['currency'],
        $meta['grand_total'],
        $json,
        $id,
    ]);
}

function cmsDeleteInvoice(PDO $pdo, int $id): void
{
    $pdo->prepare("DELETE FROM invoices WHERE source_proposal_id = ? AND record_type = 'invoice'")->execute([$id]);
    $pdo->prepare('DELETE FROM invoices WHERE id = ?')->execute([$id]);
}
