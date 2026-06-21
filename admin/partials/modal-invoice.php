<!-- Proposal / invoice create / edit form -->
<div class="modal fade" id="modalInvoiceForm" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down invoice-modal-dialog">
        <div class="modal-content invoice-modal">

            <form id="formInvoice" novalidate>

                <div class="modal-header invoice-modal__header">
                    <h5 class="modal-title" id="modalInvoiceFormTitle">New proposal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body invoice-modal__body invoice-modal__body--compact">

                    <input type="hidden" id="invoiceFormId" value="">
                    <input type="hidden" id="invoiceFormRecordType" value="proposal">
                    <input type="hidden" id="invoiceFormLinkedInvoiceId" value="">
                    <input type="hidden" id="invoiceFormLinkedInvoiceNumber" value="">

                    <div id="invoiceProposalLinkedNote" class="alert alert-info py-2 px-3 small d-none invoice-proposal-linked-note" role="status">
                        This proposal has a linked invoice (<strong id="invoiceProposalLinkedNumber"></strong>). Saving the proposal updates the invoice automatically. Use <strong>Print invoice</strong> to open the invoice PDF.
                    </div>

                    <div class="invoice-form-block">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-3 invoice-only-field" id="wrapInvoiceNumber">
                                <label class="form-label" for="invoiceFormNumber">Invoice number</label>
                                <input type="text" class="form-control invoice-field-readonly" id="invoiceFormNumber" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3" id="wrapOfferDate">
                                <label class="form-label" for="invoiceFormOfferDate" id="labelOfferDate">Offer date</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormOfferDate" name="offerDate">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 invoice-only-field" id="wrapDueDate">
                                <label class="form-label" for="invoiceFormDueDate">Due date</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormDueDate" name="dueDate">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Currency</label>
                                <div class="invoice-currency-pills" role="group" aria-label="Currency">
                                    <input class="btn-check" type="radio" name="invoiceFormCurrency" id="invoiceCurrencySar" value="SAR" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceCurrencySar">SAR</label>
                                    <input class="btn-check" type="radio" name="invoiceFormCurrency" id="invoiceCurrencyJod" value="JOD">
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceCurrencyJod">JOD</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="invoiceFormEventDate">Event date</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormEventDate" name="eventDate">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="invoiceFormLocation">Event location</label>
                                <input type="text" class="form-control" id="invoiceFormLocation" name="location" placeholder="e.g. Fairmont Hotel">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="invoiceFormPrepared">Prepared by</label>
                                <input type="text" class="form-control" id="invoiceFormPrepared" name="prepared" placeholder="e.g. Eng. Mohammad Tarifi">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="invoiceFormSubject">Subject / title</label>
                                <input type="text" class="form-control" id="invoiceFormSubject" name="subject" placeholder="e.g. Wedding of Mr. Mohammed">
                            </div>
                        </div>
                    </div>

                    <div class="invoice-form-block invoice-only-field">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormClientName">Client name</label>
                                <input type="text" class="form-control" id="invoiceFormClientName" name="clientName" placeholder="Client or company name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormClientEmail">Email</label>
                                <input type="email" class="form-control" id="invoiceFormClientEmail" name="clientEmail" placeholder="client@email.com">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormClientPhone">Phone</label>
                                <input type="text" class="form-control" id="invoiceFormClientPhone" name="clientPhone" placeholder="+966 …">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormSignatureDate">Date signed</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormSignatureDate" name="signatureDate">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="invoiceFormClientAddress">Address</label>
                                <textarea class="form-control" id="invoiceFormClientAddress" name="clientAddress" rows="2" placeholder="Street, city, country"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-form-block">
                        <div class="invoice-lines-wrap">
                            <div id="invoiceLinesBody" class="invoice-categories-wrap"></div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnInvoiceAddCategory">
                                <i class="bi bi-plus-lg"></i> Add category
                            </button>
                            <span class="invoice-lines-hint text-muted small">Each category groups its own line items (e.g. Entertainment, Furniture &amp; decorations). Use <strong>Add item</strong> inside a category for more rows.</span>
                        </div>
                    </div>

                    <div class="invoice-form-block invoice-form-block--last">
                        <div class="row g-3">
                            <div class="col-12 col-lg-5">
                                <label class="form-label" for="invoiceFormDiscount">Discount (%)</label>
                                <input type="number" class="form-control" id="invoiceFormDiscount" name="discount" min="0" max="100" step="1" value="0">
                                <label class="form-label mt-3" for="invoiceFormNotes">Notes</label>
                                <textarea class="form-control" id="invoiceFormNotes" name="notes" rows="4" placeholder="Additional notes for the client (optional)"></textarea>
                            </div>
                            <div class="col-12 col-lg-7">
                                <div class="invoice-summary-panel">
                                    <div class="invoice-totals-grid">
                                        <div><span>Subtotal (line amounts)</span><strong><span id="invoiceSubtotal">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div><span>Event management fees (15%)</span><strong><span id="invoiceFees">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div id="invoiceDiscountRow" hidden><span>Discount</span><strong>-<span id="invoiceDiscountAmt">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div class="invoice-total-grand"><span>Amount due</span><strong><span id="invoiceGrandTotal">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                    </div>
                                    <p class="invoice-summary-panel__schedule">
                                        Payment schedule:
                                        <span>30% <strong id="invoicePay1">0</strong></span>
                                        <span>40% <strong id="invoicePay2">0</strong></span>
                                        <span>30% <strong id="invoicePay3">0</strong></span>
                                        <span class="invoice-currency-label">SAR</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p id="invoiceFormError" class="alert alert-danger invoice-modal__error d-none" role="alert"></p>

                </div>

                <div class="modal-footer invoice-modal__footer">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnInvoiceMakeInvoice" hidden>
                        <i class="bi bi-receipt-cutoff"></i> Make invoice
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" id="btnInvoiceViewLinked" hidden>
                        <i class="bi bi-printer"></i> Print invoice
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnInvoicePrint" hidden>
                        <i class="bi bi-printer"></i> <span id="btnInvoicePrintLabel">Print proposal</span>
                    </button>
                    <span class="flex-grow-1"></span>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnInvoiceSave">
                        <i class="bi bi-check-lg"></i> <span id="btnInvoiceSaveLabel">Save proposal</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
