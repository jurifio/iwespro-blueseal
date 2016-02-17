var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

$(document).on('bs.roulette.add', function (e, element, button) {
    window.location = '/blueseal/prodotti/roulette?roulette=' + $(element).val();
});

$(document).on('bs.pub.product', function (e, element, button) {

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html(button.getTitle());

    $.ajax({
        url: "/blueseal/xhr/CheckProductsToBePublished",
        type: "GET"
    }).done(function (response) {
        result = JSON.parse(response);
        body.html(result.bodyMessage);

        if (result.cancelButtonLabel == null) {
            cancelButton.hide();
        } else {
            cancelButton.html(result.cancelButtonLabel);
        }

        if (result.status == 'ok') {
            okButton.html(result.okButtonLabel).off().on('click', function (e) {
                body.html(loaderHtml);
                $.ajax({
                    url: "/blueseal/xhr/CheckProductsToBePublished",
                    type: "PUT"
                }).done(function (response) {
                    result = JSON.parse(response);
                    body.html(result.bodyMessage);
                    if (result.cancelButtonLabel == null) {
                        cancelButton.hide();
                    }
                    okButton.html(result.okButtonLabel).off().on('click', function () {
                        bsModal.modal('hide');
                        okButton.off();
                    });
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

$(document).on('bs.print.aztec', function (e, element, button) {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti per avviare la stampa del codice aztec"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1] + '__' + rowId[2];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');
});

$(document).on('bs.dupe.product', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un prodotto da duplicare"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi duplicare un solo prodotto per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2] + '&double=true';
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/prodotti/modifica?' + getVars, 'product-dupe-' + Math.random() * (9999999999));
});

$(document).on('bs.add.sku', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning!",
            message: "Devi selezionare un prodotto da movimentare"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning!",
            message: "Puoi movimentare un solo prodotto per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/skus?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});

$(document).on('bs.manage.photo', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;
    var alertContainer = $('.alert-container');

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare il prodotto del quale vuoi caricare le foto"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi caricare le foto di un solo prodotto per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/prodotti/photos?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});

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
    var alertContainer = $('.alert-container');

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti da cancellare"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1] + '__' + rowId[2];
        i++;
    });

    var getVars = getVarsArray.join('&');

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
                    data: getVars
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