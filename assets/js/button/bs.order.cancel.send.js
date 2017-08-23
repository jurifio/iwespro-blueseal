window.buttonSetup = {
    tag: "a",
    icon: "fa-times-circle-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.cancel.send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Mail: Ordine Cancellato (mancante)",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.cancel.send', function (e, element, button) {

    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un ordine per inserire il tracker"
        }).open();
        return false;
    }

    var selectedRow = dataTable.row('.selected').data();
    var orderId = selectedRow.DT_RowId;

    header.html(button.getTitle());
    body.html(
        '<div class="form-group form-group-default">' +
        '<label for="lang">Lingua</label>' +
        '<select id="#lang" name="lang" class="full-width"></select>' +
        '</div>' +
        '<div class="form-group form-group-default required">' +
        '<label for="reason1">Prodotto 1</label>' +
        '<input id="reason1" autocomplete="off" type="text" class="form-control" value="" required="required">' +
        '</div>'+
        '<div class="form-group form-group-default required">' +
        '<label for="reason2">Prodotto 2</label>' +
        '<input id="reason2" autocomplete="off" type="text" class="form-control" value="" required="required">' +
        '</div>'+
        '<div class="form-group form-group-default required">' +
        '<label for="reason3">Prodotto 3</label>' +
        '<input id="reason3" autocomplete="off" type="text" class="form-control" value="" required="required">' +
        '</div>'
    );
    bsModal.modal();
    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Lang'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('select[name=\"lang\"]');
            if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
            select[0].selectize.setValue(1);
        });

        okButton.html('Invia').off().on('click', function () {
            var reasons = [];
            reasons.push($('#reason1').val());
            reasons.push($('#reason2').val());
            reasons.push($('#reason3').val());
            var langId = $('select[name=\"lang\"]').val();
            cancelButton.off().hide();
            okButton.html('Fatto').off().on('click', function () {
                bsModal.modal('hide');
            });
            body.html(loaderHtml);
            $.ajax({
                url: "/blueseal/xhr/OrderDelete",
                type: "POST",
                data: {
                    'orderId': orderId,
                    'productsIds': reasons,
                    'langId': langId
                }
            }).done(function (response) {
                body.html('fatto');
            }).fail(function (response) {
                body.html(response);
            });
        });
    });

});
