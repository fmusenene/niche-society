<!-- Proposal / invoice create / edit form -->
<div class="modal fade" id="modalInvoiceForm" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down invoice-modal-dialog">
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

                    <section class="invoice-form-section" aria-labelledby="invoiceSectionProposalTitle">
                        <h6 class="invoice-form-heading" id="invoiceSectionProposalTitle">
                            <i class="bi bi-file-earmark-text" aria-hidden="true"></i> Proposal
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 proposal-only-field" id="wrapProposalKind">
                                <label class="form-label" id="labelProposalKind">Proposal type</label>
                                <div class="invoice-currency-pills" role="group" aria-label="Proposal type">
                                    <input class="btn-check" type="radio" name="invoiceFormProposalKind" id="invoiceProposalKindEvent" value="event" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceProposalKindEvent" id="labelProposalKindEvent">Event</label>
                                    <input class="btn-check" type="radio" name="invoiceFormProposalKind" id="invoiceProposalKindService" value="service">
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceProposalKindService" id="labelProposalKindService">Service</label>
                                </div>
                            </div>
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
                                <label class="form-label" id="labelInvoiceCurrency">Currency</label>
                                <div class="invoice-currency-pills" role="group" aria-label="Currency">
                                    <input class="btn-check" type="radio" name="invoiceFormCurrency" id="invoiceCurrencySar" value="SAR" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceCurrencySar">SAR</label>
                                    <input class="btn-check" type="radio" name="invoiceFormCurrency" id="invoiceCurrencyJod" value="JOD">
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceCurrencyJod">JOD</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label" id="labelInvoiceLanguage">Language</label>
                                <div class="invoice-language-pills invoice-currency-pills" role="group" aria-label="Document language">
                                    <input class="btn-check" type="radio" name="invoiceFormLanguage" id="invoiceLangEn" value="en" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceLangEn">English</label>
                                    <input class="btn-check" type="radio" name="invoiceFormLanguage" id="invoiceLangAr" value="ar">
                                    <label class="btn btn-sm btn-outline-primary" for="invoiceLangAr">العربية</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 proposal-event-field">
                                <label class="form-label" for="invoiceFormEventDate" id="labelEventDate">Event date</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormEventDate" name="eventDate">
                            </div>
                            <div class="col-12 col-md-4 proposal-event-field">
                                <label class="form-label" for="invoiceFormLocation" id="labelEventLocation">Event location</label>
                                <input type="text" class="form-control" id="invoiceFormLocation" name="location" placeholder="e.g. Fairmont Hotel">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="invoiceFormPrepared" id="labelPreparedBy">Prepared by</label>
                                <input type="text" class="form-control" id="invoiceFormPrepared" name="prepared" placeholder="e.g. Eng. Mohammad Tarifi">
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label" for="invoiceFormSubject" id="labelSubject">Subject / title</label>
                                <input type="text" class="form-control" id="invoiceFormSubject" name="subject" placeholder="e.g. Wedding of Mr. Mohammed">
                            </div>
                            <div class="col-12 col-md-4 proposal-only-field">
                                <label class="form-label" for="invoiceFormProposalTel" id="labelTel">Tel</label>
                                <input type="text" class="form-control" id="invoiceFormProposalTel" name="proposalTel" placeholder="+966 …">
                            </div>
                        </div>
                    </section>

                    <section class="invoice-form-section" id="invoiceSectionClient" aria-labelledby="invoiceSectionClientTitle">
                        <h6 class="invoice-form-heading" id="invoiceSectionClientTitle">
                            <i class="bi bi-person-lines-fill" aria-hidden="true"></i> Client details
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormClientName" id="labelClientName">Client name</label>
                                <input type="text" class="form-control" id="invoiceFormClientName" name="clientName" placeholder="Client or company name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invoiceFormClientEmail" id="labelClientEmail">Email</label>
                                <input type="email" class="form-control" id="invoiceFormClientEmail" name="clientEmail" placeholder="client@email.com">
                            </div>
                            <div class="col-12 col-md-6 invoice-only-field">
                                <label class="form-label" for="invoiceFormClientPhone" id="labelClientPhone">Phone</label>
                                <input type="text" class="form-control" id="invoiceFormClientPhone" name="clientPhone" placeholder="+966 …">
                            </div>
                            <div class="col-12 col-md-6 invoice-only-field">
                                <label class="form-label" for="invoiceFormSignatureDate" id="labelDateSigned">Date signed</label>
                                <input type="date" class="form-control invoice-date-input" id="invoiceFormSignatureDate" name="signatureDate">
                            </div>
                            <div class="col-12 invoice-only-field">
                                <label class="form-label" for="invoiceFormClientAddress" id="labelClientAddress">Address</label>
                                <textarea class="form-control" id="invoiceFormClientAddress" name="clientAddress" rows="2" placeholder="Street, city, country"></textarea>
                            </div>
                        </div>
                    </section>

                    <section class="invoice-form-section invoice-form-section--categories" aria-labelledby="invoiceSectionCategoriesTitle">
                        <h6 class="invoice-form-heading" id="invoiceSectionCategoriesTitle">
                            <i class="bi bi-list-ul" aria-hidden="true"></i> Categories &amp; line items
                        </h6>
                        <div class="invoice-lines-wrap">
                            <div id="invoiceLinesBody" class="invoice-categories-wrap"></div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnInvoiceAddCategory">
                                <i class="bi bi-plus-lg"></i> <span id="labelAddCategory">Add category</span>
                            </button>
                            <span class="invoice-lines-hint text-muted small" id="invoiceLinesHint">Group services by category. Use <strong>Add item</strong> inside each category for more rows.</span>
                        </div>
                    </section>

                    <section class="invoice-form-section invoice-form-section--last" aria-labelledby="invoiceSectionTotalsTitle">
                        <h6 class="invoice-form-heading" id="invoiceSectionTotalsTitle">
                            <i class="bi bi-calculator" aria-hidden="true"></i> Totals
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-lg-5">
                                <div id="invoiceEventTotalsFields">
                                    <label class="form-label" for="invoiceFormDiscount" id="labelDiscount">Discount (%)</label>
                                    <input type="number" class="form-control" id="invoiceFormDiscount" name="discount" min="0" max="100" step="1" value="0">
                                </div>
                                <div id="invoiceServiceTotalsFields" class="d-none">
                                    <label class="form-label" for="invoiceFormServicePriceExcl" id="labelServicePriceExcl">Package price (excl. tax)</label>
                                    <input type="number" class="form-control" id="invoiceFormServicePriceExcl" name="servicePriceExclTax" min="0" step="1" value="0">
                                    <label class="form-label mt-3" for="invoiceFormServicePriceIncl" id="labelServicePriceIncl">Package price (incl. tax)</label>
                                    <input type="number" class="form-control" id="invoiceFormServicePriceIncl" name="servicePriceInclTax" min="0" step="1" value="0">
                                    <label class="form-label mt-3" for="invoiceFormTaxRate" id="labelTaxRate">Tax (%)</label>
                                    <input type="number" class="form-control" id="invoiceFormTaxRate" name="taxRate" min="0" max="100" step="1" value="15">
                                    <p class="small text-muted mt-2 mb-0" id="invoiceServiceTaxHint">Enter excl. or incl. price — the other is calculated using this tax rate.</p>
                                    <label class="form-label mt-3" for="invoiceFormBankDetails" id="labelBankDetails">Bank details</label>
                                    <textarea class="form-control" id="invoiceFormBankDetails" name="bankDetails" rows="4" placeholder="Bank name, account name, IBAN…"></textarea>
                                </div>
                                <label class="form-label mt-3" for="invoiceFormNotes" id="labelNotes">Notes</label>
                                <textarea class="form-control" id="invoiceFormNotes" name="notes" rows="4" placeholder="Additional notes for the client (optional)"></textarea>
                            </div>
                            <div class="col-12 col-lg-7">
                                <div class="invoice-summary-panel">
                                    <div class="invoice-totals-grid" id="invoiceEventTotalsPanel">
                                        <div><span id="labelSubtotal">Subtotal (line amounts)</span><strong><span id="invoiceSubtotal">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div><span id="labelFees">Event management fees (15%)</span><strong><span id="invoiceFees">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div id="invoiceDiscountRow" hidden><span id="labelDiscountAmt">Discount</span><strong>-<span id="invoiceDiscountAmt">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div class="invoice-total-grand"><span id="labelAmountDue">Amount due</span><strong><span id="invoiceGrandTotal">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                    </div>
                                    <div class="invoice-totals-grid d-none" id="invoiceServiceTotalsPanel">
                                        <div><span id="labelServiceSummaryExcl">Price (excl. tax)</span><strong><span id="invoiceServiceSummaryExclAmt">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div><span id="labelServiceSummaryTax">Tax (15%)</span><strong><span id="invoiceServiceSummaryTaxAmt">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                        <div class="invoice-total-grand"><span id="labelServiceSummaryIncl">Price (incl. tax)</span><strong><span id="invoiceServiceSummaryInclAmt">0</span> <span class="invoice-currency-label">SAR</span></strong></div>
                                    </div>
                                    <p class="invoice-summary-panel__schedule">
                                        <span id="labelPaymentSchedule">Payment schedule:</span>
                                        <span>30% <strong id="invoicePay1">0</strong></span>
                                        <span>40% <strong id="invoicePay2">0</strong></span>
                                        <span>30% <strong id="invoicePay3">0</strong></span>
                                        <span class="invoice-currency-label">SAR</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <p id="invoiceFormError" class="alert alert-danger invoice-modal__error d-none" role="alert"></p>

                </div>

                <div class="modal-footer invoice-modal__footer">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnInvoiceMakeInvoice" hidden>
                        <i class="bi bi-receipt-cutoff"></i> <span id="labelMakeInvoice">Make invoice</span>
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" id="btnInvoiceViewLinked" hidden>
                        <i class="bi bi-printer"></i> <span id="labelPrintLinkedInvoice">Print invoice</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnInvoicePrint" hidden>
                        <i class="bi bi-printer"></i> <span id="btnInvoicePrintLabel">Print proposal</span>
                    </button>
                    <span class="flex-grow-1"></span>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="labelCancel">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnInvoiceSave">
                        <i class="bi bi-check-lg"></i> <span id="btnInvoiceSaveLabel">Save proposal</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Choose installment before printing an invoice -->
<div class="modal fade" id="modalPrintInstallment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPrintInstallmentTitle">Print invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="modalPrintInstallmentDesc">Select which payment this invoice is for — one installment or the full contract amount.</p>
                <div class="d-grid gap-2 invoice-installment-choices">
                    <button type="button" class="btn btn-primary btn-installment-print" data-installment="full">
                        <span id="labelInstallmentFull">Full payment (100%)</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-installment-print" data-installment="1">
                        <span id="labelInstallment1">1st payment (30%)</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-installment-print" data-installment="2">
                        <span id="labelInstallment2">2nd payment (40%)</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-installment-print" data-installment="3">
                        <span id="labelInstallment3">3rd payment (30%)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
