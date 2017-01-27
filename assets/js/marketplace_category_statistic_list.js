$(document).ready(function () {
    "use strict";
    $('ul.breadcrumb').append($('<li><p style="display: inline-block">'+$('table.table').data('specialName')+'</p></li>'));
});

$(document).on('bs.marketplace.category.delete', function(a,b,c){
    "use strict";

    var table = $('.table');
    var dt = table.DataTable();
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Cancella Categorie Prodotti');

    var getVarsArray = [];
    var selectedRows = dt.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Categorie per poterle cancellare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.marketCode);
    });

    body.html('<div>Sei sicuro di voler togliere dal marketplace ' + getVarsArray.lenght + ' Categorie?</div>');

    var asd = table.data('marketplaceId');
    cancelButton.show();
    okButton.html('Esegui').show().off().on('click', function () {
        cancelButton.hide();
        okButton.hide();
        body.html('<img src="/assets/img/ajax-loader.gif" />');
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/MarketplaceCategoryProductManageController',
                type: "DELETE",
                data: {
                    marketplaceId: asd,
                    categories: getVarsArray
                }
            }).done(function (response) {
                body.html('Eliminati '+response+' prodotti');
                okButton.html('Ok');
                okButton.off().on('click',function () {
                    bsModal.modal('hide');
                });
            });
        });
    });

    bsModal.modal();
});