window.buttonSetup = {
    tag:"a",
    icon:"fa-trash",
    permission:"/admin/product/delete&&allShops",
    event:"bs.del.product",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Elimina prodotto",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.del.product', function (e, element, button) {

    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti da cancellare"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = v.DT_RowId;
        i++;
    });

    var getVars = 'id[]='+getVarsArray.join('&id[]=');

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    header.html(button.getTitle());

    $.ajax({
        url: "/blueseal/xhr/DeleteProduct",
        type: "GET",
        data: getVars
    }).done(function (response) {
        result = JSON.parse(response);
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
