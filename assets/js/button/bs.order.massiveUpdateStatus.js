window.buttonSetup = {
    tag: "a",
    icon: "fa-envelop   e",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.massiveUpdateStatus",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Mail: Ordine non pagato",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.massiveUpdateStatus', function (e, element, button) {

    var dataTable = $('.dataTable').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 < selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un ordine"
        }).open();
        return false;
    }

    var orders = [];
    $.each(selectedRows, function(k,v) {
        "use strict";
        orders.push(v.DT_RowId);
    });

    modal.
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
            body.html(loaderHtml);
            $.ajax({
                url: "/blueseal/xhr/OrderRecallClient",
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