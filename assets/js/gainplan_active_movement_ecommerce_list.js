$(document).on('bs.gainplanactivemovement.disable', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo documento"
        }).open();
        return false;
    }

    var documentId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Disabilita  la visualizzazione del documento',
        {
            body: 'Proseguendo sarà disabilitato per sempre il documento <strong>' + documentId + '</strong>!<br />',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/GainPlanMovementDisableManage',
                        method: 'POST',
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

$(document).on('bs.gainplanactivemovement.enable', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo documento"
        }).open();
        return false;
    }

    var documentId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Abilita  la visualizzazione del documento!',
        {
            body: 'Proseguendo sarà abilitato il documento <strong>' + documentId + '</strong>!<br />',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/GainPlanMovementDisableManage',
                        method: 'PUT',
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