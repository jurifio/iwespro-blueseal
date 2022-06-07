$(document).on('bs.invoice.delete', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una sola Fattura"
        }).open();
        return false;
    }

    var invoiceId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Elimina fisicamente e in modo permanente la Fattura!',
        {
            body: 'Proseguendo sarà eliminato per sempre la fattura  <strong>' + invoiceId + '</strong>!<br />' +
                'Pensaci un momento prima di proseguire!',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/InvoiceManage',
                        method: 'DELETE',
                        data: {invoiceId: invoiceId}
                    }
                ).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            }
        }
    );
});