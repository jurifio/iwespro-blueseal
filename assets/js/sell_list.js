
/**
 * Created by Fabrizio Marconi on 22/10/2015.
 */
$(document).on('bs.order.delete.panic', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo prodotto"
        }).open();
        return false;
    }

    var orderId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Elimina fisicamente e in modo permanente l\'ordine!',
        {
            body: 'Proseguendo sarà eliminato per sempre l\'ordine <strong>' + orderId + '</strong>!<br />' +
                'Pensaci un momento prima di proseguire!',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/OrderListAjaxController',
                        method: 'DELETE',
                        data: {orderId: orderId}
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