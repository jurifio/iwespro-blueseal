window.buttonSetup = {
    tag: "a",
    icon: "fa-ship",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.tracker.send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Invia Codice Tracker",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.tracker.send', function (e, element, button) {

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
        '<label for="reason">Motivo</label>' +
        '<input id="reason" autocomplete="off" type="text" class="form-control" value="" required="required">' +
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
            var tracking = $('#reason').val();
            if (tracking.length < 1) {
                new Alert({
                    type: "warning",
                    message: "Devi inserire il motivo per inserire la mail"
                }).open();
                return false;
            }
            cancelButton.off().hide();
            okButton.html('Fatto').off().on('click', function () {
                bsModal.modal('hide');
            });
            $.ajax({
                url: "/blueseal/xhr/OrderTracker",
                type: "POST",
                data: {
                    'orderId': orderId,
                    'tracking': reason,
                    'langId': $('select[name=\"lang\"]').val()
                }
            }).done(function (response) {
                body.html('fatto');
            }).fail(function (response) {
                body.html(response);
            });
        });
    });

});
