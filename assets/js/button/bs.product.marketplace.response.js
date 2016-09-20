window.buttonSetup = {
    tag:"a",
    icon:"fa-file-code-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs.product.marketplace.response",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Leggi Stato",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.product.marketplace.response', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    cancelButton.hide();
    var okButton = $('.modal-footer .btn-success');
    okButton.html('Ok');
    header.html('Risposta Marketplaces');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Prodotto"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductResponse',
            type: "get",
            data: {
                rows: getVarsArray
            }
        }).done(function (res) {
            body.html(res);
            bsModal.modal();
        });
    });
});
