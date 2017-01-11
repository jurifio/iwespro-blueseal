window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/edit&&allShops",
    event: "bs.productHasMarketplace.marketplace.delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella prodotto dal marketplace",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.productHasMarketplace.marketplace.delete', function (e, element, button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Cancella Prodotti');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli cancellare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.marketCode);
    });

    body.html('<div>Sei sicuro di voler togliere dal marketplace ' + getVarsArray.lenght + ' Prodotti?</div>');

    okButton.off().on('click', function () {
        cancelButton.hide();
        okButton.hide();
        body.html('<img src="/assets/img/ajax-loader.gif" />');
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/MarketplaceProductManageController',
                type: "DELETE",
                data: {
                    ids: getVarsArray
                }
            }).done(function (response) {
                body.html('Eliminati '+response+' prodotti');
                okButton.html('Ok');
                okButton.off().on('click',function () {
                    bsModal.hide();
                });
            });
        });
    });

    bsModal.modal();
});

$(document).on('change', '#accountId', function () {
    //window.x = $(this);
    $('#modifier').val($(this).find(':selected').data('modifier'));
    if ($(this).find(':selected').data('hasCpc')) {
        $("#cpc").parent().show();
    } else {
        $("#cpc").parent().hide();
    }
});
