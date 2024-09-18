$(document).ready(function () {
    // Flatpickr date picker initialization options
    const flatpickrOptions = {
        dateFormat: "Y-m-d",
        allowInput: true,
        locale: "sq"
    };
    // Date fields to initialize with flatpickr
    const dateFields = ['#start_date1', '#end_date1', '#startDateBiznes', '#endDateBiznes'];
    dateFields.forEach(id => {
        if ($(id).length) { // Ensure the element exists
            flatpickr(id, {
                ...flatpickrOptions,
                maxDate: id.includes('end') ? new Date() : new Date(new Date().setDate(new Date().getDate() - 3))
            });
        } else {
            console.error(`Element with ID ${id} not found.`);
        }
    });

    // DataTable initialization function
    const dataTableOptions = (ajaxUrl, start, end, includeUploadButton = false) => ({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl,
            type: "GET",
            data: d => {
                d[start.slice(1)] = $(start).val(); // Remove '#' from the start
                d[end.slice(1)] = $(end).val(); // Remove '#' from the end
            }
        },
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend: "pdf", text: '<i class="fi fi-rr-file-pdf"></i> PDF', className: "btn btn-light btn-sm bg-light border me-2 rounded-5" },
            { extend: "excelHtml5", text: '<i class="fi fi-rr-file-excel"></i> Excel', className: "btn btn-light btn-sm bg-light border me-2 rounded-5" },
            { extend: "print", text: '<i class="fi fi-rr-print"></i> Printo', className: "btn btn-light btn-sm bg-light border me-2 rounded-5" }
        ],
        columns: [
            { data: "customer_name" },
            {
                data: "payment_date",
                render: function (data) {
                    return new Date(data).toLocaleDateString('sq-AL', { year: 'numeric', month: '2-digit', day: '2-digit' });
                }
            },
            { data: "bank_info" },
            {
                data: "description",
                render: function (data, type, row) {
                    if (!data || data.length <= 30) {
                        return data || ''; // Display full description if it's short or empty
                    }
                    const shortened = data.slice(0, 30) + '...';
                    return `<span class="description-short">${shortened}</span>
                            <span class="description-full" style="display:none;">${data}</span>
                            <a href="#" class="toggle-description" aria-label="Toggle description">
                                <i class="fas fa-chevron-down"></i>
                            </a>`;
                }
            },
            { data: "total_payment_amount" },
            {
                data: null,
                title: "F. EUR",
                render: function (data, type, row) {
                    // Parse the necessary fields to ensure they are numbers
                    const totalInvoiceAmount = parseFloat(row.total_invoice_amount) || 0;
                    const totalAmountAfterPercentage = parseFloat(row.total_amount_after_percentage) || 0;

                    // Calculate the remaining amount by subtracting total_amount_after_percentage from total_invoice_amount
                    const remainingAmount = totalInvoiceAmount - totalAmountAfterPercentage;

                    // If the render type is 'display', format the number to two decimal places
                    if (type === 'display') {
                        return remainingAmount.toFixed(2);
                    }

                    // For other types (e.g., 'sort', 'filter'), return the raw number
                    return remainingAmount;
                }
            },
            {
                data: null,
                render: (data, type, row) => {
                    let buttons = [
                        `<a href="print_invoice.php?id=${data.invoice_number}" style="text-decoration: none; text-transform: none;" class="input-custom-css px-3 py-2 mx-1"><i class="fi fi-rr-print"></i></a>`,
                        row.emailadd ? `<a href="#" style="text-decoration: none; text-transform: none;" class="input-custom-css px-3 py-2 mx-1 send-invoice" data-id="${row.id}"><i class="fi fi-rr-envelope"></i></a>` : '',
                        row.email_kontablist ? `<a href="#" style="text-decoration: none; text-transform: none;" class="input-custom-css px-3 py-2 mx-1 send-invoices" data-id="${row.id}"><i class="fi fi-rr-envelope-plus"></i></a>` : ''
                    ];

                    if (includeUploadButton) {
                        if (data.file_path) {
                            // Button to view the file and open the modal for updating
                            buttons.push(
                                `<a href="${data.file_path}" style="text-decoration: none; text-transform: none;" class="input-custom-css px-3 py-2 mx-1" target="_blank"><i class="fi fi-rr-download"></i></a>`,
                                `<button type="button" class="input-custom-css px-3 py-2 mx-1" style="text-decoration: none; text-transform: none;" data-bs-toggle="modal" data-bs-target="#fileUploadModal-${row.id}">
                                    <i class="fi fi-rr-edit"></i>
                                </button>`,
                                `<div class="modal fade" id="fileUploadModal-${row.id}" tabindex="-1" aria-labelledby="fileUploadModalLabel-${row.id}" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="fileUploadModalLabel-${row.id}">Update or Remove File</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <form id="fileUploadForm-${row.id}" enctype="multipart/form-data">
                                            <input type="hidden" name="invoice_id" value="${row.id}">
                                            <div class="mb-3">
                                              <label for="description-${row.id}" class="form-label">Description</label>
                                              <input type="text" name="description" class="form-control rounded-5 border border-2" id="description-${row.id}" value="${data.file_description || ''}">
                                            </div>
                                            <div class="mb-3">
                                              <label for="fileInput-${row.id}" class="form-label">Select new file (PDF or DOC)</label>
                                              <input type="file" name="file" class="form-control rounded-5 border border-2" id="fileInput-${row.id}" accept=".pdf,.doc,.docx">
                                            </div>
                                            <button type="submit" class="input-custom-css px-3 py-2">Upload New File</button>
                                          </form>
                                          <hr>
                                          <form id="fileRemoveForm-${row.id}">
                                            <input type="hidden" name="invoice_id" value="${row.id}">
                                            <button type="submit" class="input-custom-css px-3 py-2 btn-danger">Remove File</button>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                </div>`
                            );
                        } else {
                            // Upload button if no file exists
                            buttons.push(
                                `<button type="button" class="input-custom-css px-3 py-2 mx-1" style="text-decoration: none; text-transform: none;" data-bs-toggle="modal" data-bs-target="#fileUploadModal-${row.id}">
                                    <i class="fi fi-rr-upload"></i>
                                </button>`,
                                `<div class="modal fade" id="fileUploadModal-${row.id}" tabindex="-1" aria-labelledby="fileUploadModalLabel-${row.id}" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="fileUploadModalLabel-${row.id}">Upload File</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <form id="fileUploadForm-${row.id}" enctype="multipart/form-data">
                                            <input type="hidden" name="invoice_id" value="${row.id}">
                                            <div class="mb-3">
                                                <label for="descriptionOfFile-${row.id}" class="form-label">Përshkrimi</label>
                                                <input type="text" name="descriptionOfFile" class="form-control rounded-5 border border-2" id="descriptionOfFile-${row.id}" required>
                                            </div>
                                            <div class="mb-3">
                                              <label for="fileInput-${row.id}" class="form-label">Select file (PDF or DOC)</label>
                                              <input type="file" name="file" class="form-control rounded-5 border border-2" id="fileInput-${row.id}" accept=".pdf,.doc,.docx" required>
                                            </div>
                                            <button type="submit" class="input-custom-css px-3 py-2">Upload</button>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                </div>`
                            );
                        }
                    }

                    return buttons.join('');
                }
            }
        ],
        stripeClasses: ['stripe-color'],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
        }
    });

    // Initialize DataTables
    const table = $('#paymentsTable').DataTable(dataTableOptions("api/get_methods/get_complete_invoices.php", '#start_date1', '#end_date1', false));
    const table_off_payment_biznes = $('#paymentsTableBiznes').DataTable(dataTableOptions("api/get_methods/get_complete_invoices_biznes.php", '#startDateBiznes', '#endDateBiznes', true));

    // Refresh table on date change
    const refreshTable = table => table.ajax.reload();
    dateFields.forEach(id => {
        $(id).on('change', () => refreshTable(table));
    });

    // Clear filters button functionality
    $('#clearFiltersBtn').on('click', () => {
        dateFields.forEach(id => {
            $(id).val('');
        });
        refreshTable(table);
    });

    // Update full description visibility
    $(document).on('click', '.toggle-description', function (e) {
        e.preventDefault();
        const $parent = $(this).closest('td');
        $parent.find('.description-short, .description-full').toggle();
    });

    // AJAX form submissions for file upload and removal
    $(document).on('submit', 'form[id^="fileUploadForm-"], form[id^="fileRemoveForm-"]', function (e) {
        e.preventDefault();

        const $form = $(this);
        const formData = new FormData($form[0]);
        const $modal = $form.closest('.modal');

        $.ajax({
            url: $form.attr('id').startsWith('fileUploadForm') ? 'upload_invoice_manual.php' : 'remove_file_manual.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                table_off_payment_biznes.ajax.reload();

                // Close the modal
                $modal.modal('hide');

                // Remove the backdrop manually
                $('.modal-backdrop').remove();

                // Remove the 'modal-open' class from the body
                $('body').removeClass('modal-open');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);

                // Optionally, close the modal on error as well
                $modal.modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }
        });
    });
});
