window.buttonSetup = {
    tag:"a",
    icon:"fa-percent",
    permission:"/admin/product/edit&&allShops",
    event:"bs.sales.price",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia prezzi ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.sales.price', function () {
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (!selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.brand;
        row[i].cpf = v.CPF;
        row[i].brand = v.brand;
        row[i].shops = v.shops;
        row[i].price = v.price;
        row[i].sale = v.sale;
        row[i].percentage = v.percentage;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });
    console.log(selectedRows);
    console.log(row);
    header.html('Assegna sconti');

    body.html('<form>' +
        '<div class="container-fluid">' +
        '<div class="row">' +
        '<div class="col-xs-4">' +
        '<label for="percentage">Percentuale di sconto: </label></div>' +
        '<div class="col-xs-8">' +
        '<input type="text" maxlength="2" name="percentage" class="percentage" />' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</form>');

    body.css("text-align", 'left');
    bsModal.modal('show');

    okButton.off().on('click', function () {
        $.ajax({
            url: "/blueseal/xhr/ProductSales",
            method: "POST",
            data: {
                action: "assign",
                rows: row,
                percentage: $('input[name="percentage"]').val()
            }
        }).done(function (res, a, b) {
            body.html(res);
            cancelButton.hide();
            okButton.html('ok').off().on("click", function () {
                bsModal.modal('hide');
                dataTable.ajax.reload(null, false);
            });
        });
    });
});
