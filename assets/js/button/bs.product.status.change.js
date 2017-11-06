window.buttonSetup = {
    tag:"a",
    icon:"fa-eye",
    permission:"/admin/product/edit&&allShops",
    event:"bs-manage-changeStatus",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Status ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-manage-changeStatus', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    var fused = false;
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.name;
        if ('Fuso' == v.status) fused = true;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    if (!fused) {
        $.ajax({
            url: "/blueseal/xhr/ProductStatusList",
            type: "GET",
        }).done(function (res) {
            res = JSON.parse(res);
            console.log(res);
            header.html('Cambio stato dei prodotti');
            var bodyContent = '<div style="min-height: 220px"><select class="full-width" placehoder="Seleziona lo status" name="productStatusId" id="productStatusId"><option value=""></option></select></div>';
            body.html(bodyContent);
            $('#productStatusId').selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res
            });
            $('#productStatusId').selectize()[0].selectize.setValue(1);
        });
        cancelButton.html("Annulla");
        cancelButton.show();

        bsModal.modal('show');

        okButton.html("Cambia Stato").off().on('click', function (e) {
            var statusId = $('#productStatusId').val();
            Pace.ignore(function () {
                $.ajax({
                    url: "/blueseal/xhr/CheckProductsToBePublished",
                    type: "POST",
                    data: {
                        action: 'updateProductStatus',
                        rows: row,
                        productStatusId: statusId
                    }
                }).done(function (res) {
                    body.html(res);
                }).fail(function () {
                    body.html("OOPS! Modifica non eseguita!");
                }).always(function () {
                    okButton.html('Ok');
                    okButton.off().on('click', function () {
                        bsModal.modal('hide');
                        dataTable.ajax.reload(null, false);
                    });
                });
            });
        });
    } else { //if !fused
        header.html('Cambio stato dei prodotti');
        var bodyContent = 'Lo stato "Fuso" non può essere modificato';
        body.html(bodyContent);
        cancelButton.hide();
        okButton.html("Ok").off().on('click', function () {
            bsModal.modal("hide");
        });
        bsModal.modal();
    }
    bsModal.modal();
});
