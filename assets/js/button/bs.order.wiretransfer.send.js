window.buttonSetup = {
    tag: "a",
    icon: "fa-university",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.wiretransfer.send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Mail: Ordine con bonifico",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.wiretransfer.send', function (e, element, button) {

    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un ordine per inserire il tracker"
        }).open();
        return false;
    }

    var orders = [];
    $.each(selectedRows, function(k,v) {
        "use strict";
        orders.push(v.DT_RowId);
    });

    header.html(button.getTitle());
    body.html(
        '<div class="form-group form-group-default">' +
        '<label for="lang">lingua</label>' +
        '<select id="#lang" name="lang" class="full-width"></select>' +
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
            cancelButton.off().hide();
            okButton.html('Fatto').off().on('click', function () {
                bsModal.modal('hide');
            });
            var lang = $('select[name=\"lang\"]').val();
            body.html(loaderHtml)
            $.ajax({
                url: "/blueseal/xhr/OrderWireTransferMailClient",
                type: "POST",
                data: {
                    'ordersId': orders,
                    'langId':lang
                }
            }).done(function (response) {
                body.html('fatto');
            }).fail(function (response) {
                body.html(response);
            });
        });
    });

});
