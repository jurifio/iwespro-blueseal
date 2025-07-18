window.buttonSetup = {
    tag:"a",
    icon:"fa-ticket",
    permission:"/admin/product/edit&&allShops",
    event:"bs-sales-set",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Metti in saldo i prodotti Selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-sales-set', function () {
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
    });
    console.log(selectedRows);
    console.log(row);
    header.html('Imposta le promozioni');

    body.html('<form>' +
        '<div class="container-fluid">' +
        '<div class="row">' +
        '<div class="col-xs-9">' +
        '<label for="isSale">Avviare gli sconti per i prodotti selezionati?</label></div>' +
        '<div class="col-xs-3">' +
        '<input type="radio" name="isSale" class="checkit" value="1"/> Sì<br /><input type="radio" name="isSale" value="0"/> No' +
        '<input type="hidden" name="isOnSale" value="1" />' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</form>');
    body.css("text-align", 'left');
    bsModal.modal('show');

    okButton.off().on('click', function () {
        var val = $('input[name="isSale"]:checked').val();
        if (val) {
            $.ajax({
                url: "/blueseal/xhr/ProductSales",
                method: "POST",
                data: {
                    action: "set",
                    rows: row,
                    isSale: val
                }
            }).done(function (res, a, b) {
                body.html(res);
                cancelButton.hide();
                okButton.html('ok').off().on("click", function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload(null, false);
                });
            });
        }
    });
});
