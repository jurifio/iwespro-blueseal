$(document).on('bs.timetable.modify', function () {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un riga alla Volta per registrare  il pagamento"
        }).open();
        return false;
    }
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd+'T00:00';
    let billRegistryTimeTableId = selectedRows[0].DT_RowId;
    let paymentAmount = selectedRows[0].amountPayment;
    let bsModal = new $.bsModal('Registra Pagamento ', {
        body: '<p>Confermare?</p>' +
            '<div class="row">' +
            '<label for="amountPayment">Importo Scadenza</label>' +
            '<input autocomplete="off" type="text" id="amountPayment" ' +
            'class="form-control" name="amountPayment" value="' + paymentAmount + '"></div>'+
            '<div class="row">' +
            '<label for="datePayment">Data Pagamento</label>' +
            '<input autocomplete="off" type="datetime-local" id="datePayment" ' +
            'class="form-control" name="amountPayment" value="' + today + '"></div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = {billRegistryTimeTableId:billRegistryTimeTableId,amountPayment:$('#amountPayment').val(),datePayment:$('#datePayment').val()};
        var urldef = "/blueseal/xhr/BillRegistryTimeTableManageController";
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
            });

        });
    });

});