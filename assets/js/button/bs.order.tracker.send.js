window.buttonSetup = {
    tag:"a",
    icon:"fa-trash",
    permission:"/admin/product/delete&&allShops",
    event:"bs.order.tracker.send",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Invia Codice Tracker",
    placement:"bottom",
    toggle:"modal"
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
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount == 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un ordine per inserire il tracker"
        }).open();
        return false;
    }

    var orderId = selectedRows.eq(0).DT_RowId;

    header.html(button.getTitle());
    body.html('');
    okButton.html('Invia').off().on('click',function () {
        $('tracking')
        $.ajax({
            url: "/blueseal/xhr/OrderTracker",
            type: "POST",
            data: {
                'orderId': orderId,
                'tracking': orderId
            }
        }).done(function (response) {
            body.html(result.bodyMessage);
            $(bsModal).find('table').addClass('table');

            if (result.cancelButtonLabel == null) {
                cancelButton.hide();
            } else {
                cancelButton.html(result.cancelButtonLabel);
            }
            bsModal.modal('show');
            if (result.status == 'ok') {
                okButton.html(result.okButtonLabel).off().on('click', function (e) {
                    body.html(loaderHtml);
                    $.ajax({
                        url: "/blueseal/xhr/DeleteProduct",
                        type: "DELETE",
                        data: {
                            ids: getVarsArray
                        }
                    }).done(function (response) {
                        result = JSON.parse(response);
                        body.html(result.bodyMessage);
                        $(bsModal).find('table').addClass('table');
                        if (result.cancelButtonLabel == null) {
                            cancelButton.hide();
                        }
                        okButton.html(result.okButtonLabel).off().on('click', function () {
                            bsModal.modal('hide');
                            okButton.off();
                            dataTable.ajax.reload(null, false);
                        });
                        dataTable.draw();
                        bsModal.modal('show');
                    });
                });
            } else if (result.status == 'ko') {
                okButton.html(result.okButtonLabel).off().on('click', function () {
                    bsModal.modal('hide');
                    okButton.off();
                });
            }
        });
    });

});
