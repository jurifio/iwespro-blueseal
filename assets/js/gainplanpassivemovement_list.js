$(document).on('bs.gainplanpassivemovement.delete', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo documetno"
        }).open();
        return false;
    }

    var documentId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Elimina fisicamente e in modo permanente l\'ordine!',
        {
            body: 'Proseguendo sarà eliminato per sempre l\'ordine <strong>' + documentId + '</strong>!<br />' +
                'Pensaci un momento prima di proseguire!',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/GainPlanPassiveMovementManage',
                        method: 'DELETE',
                        data: {id: documentId}
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