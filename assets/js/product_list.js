$(document).on('bs.roulette.add', function (e, element, button) {
    window.location = '/blueseal/prodotti/roulette?roulette=' + $(element).val();
});

$(document).on('bs.sales.price', function () {
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (!selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.brand;
        row[i].cpf = v.CPF;
        row[i].brand = v.brand;
        row[i].shops = v.shops;
        row[i].price = v.price;
        row[i].sale = v.sale;
        row[i].percentage = v.percentage;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });
    console.log(selectedRows);
    console.log(row);
    header.html('Assegna sconti');

    body.html('<form>' +
        '<div class="container-fluid">' +
        '<div class="row">' +
        '<div class="col-xs-4">' +
        '<label for="percentage">Percentuale di sconto: </label></div>' +
        '<div class="col-xs-8">' +
        '<input type="text" maxlength="2" name="percentage" class="percentage" />' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</form>');

    body.css("text-align", 'left');
    bsModal.modal('show');

    okButton.off().on('click', function () {
        $.ajax({
            url: "/blueseal/xhr/ProductSales",
            method: "POST",
            data: {
                action: "assign",
                rows: row,
                percentage: $('input[name="percentage"]').val()
            }
        }).done(function (res, a, b) {
            body.html(res);
            cancelButton.hide();
            okButton.html('ok').off().on("click", function () {
                bsModal.modal('hide');
                dataTable.ajax.reload(null, false);
            });
        });
    });
});

$(document).on('bs.product.mergedetails', function () {
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (!selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.brand;
        row[i].cpf = v.CPF;
        row[i].brand = v.brand;
        row[i].shops = v.shops;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    header.html('Fondi i dettagli');

    body.css("text-align", 'left');

    $.ajax({
        url: '/blueseal/xhr/ProductDetailsMerge',
        type: 'GET',
        data: {rows: row}
    }).done(function (res) {
        res = JSON.parse(res);
        var bodyContent = '<div style="min-height: 250px"><p>Seleziona il prodotto da usare come modello:</p><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productCodeSelect" id="productCodeSelect"><option value=""></option></select></div>';
        bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
        bodyContent += '<input id="productCodeName" autocomplete="off" type="text" class="form-control" name="productCodeName" title="productCodeName" value="">';
        body.html(bodyContent);
        $('#productCodeSelect').selectize({
            valueField: 'code',
            labelField: 'code',
            searchField: 'code',
            options: res,
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        escape(item.code) + " - " + escape(item.variant) +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 >= query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/ProductDetailsMerge',
                    type: 'GET',
                    data: {
                        search: query
                    },
                    dataType: 'json',
                    error: function () {
                        callback();
                    },
                    success: function (res) {
                        callback(res);
                    }
                });
            }
        });
        $('#productCodeSelect').selectize()[0].selectize.setValue(row[0].id);

        var detName = $('#productCodeSelect option:selected').text().split('(')[0];
        $('#productCodeName').val(detName);

        $(bsModal).find('table').addClass('table');
        $('#productCodeSelect').change(function () {
            var detName = $('#productCodeSelect option:selected').text().split('(')[0];
            $('#productCodeName').val(detName);
        });

        cancelButton.html("Annulla").show().on('click', function () {
            bsModal.hide();
        });

        okButton.html("Copia Dettagli!").off().on('click', function () {
            $.ajax({
                url: '/blueseal/xhr/ProductDetailsMerge',
                type: 'POST',
                data: {rows: row, choosen: $('#productCodeName').val()}
            }).done(function (res) {
                body.html(res);
                cancelButton.hide();
                okButton.html("Ok").off().on('click', function () {
                    bsModal.modal("hide");
                    dataTable.ajax.reload(null, false);
                });
            });
        });
    });
    bsModal.modal('show');
});

$(document).on('bs.product.mergenames', function () {

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
            message: "Devi selezionare almeno un dettaglio da unire"
        }).open();
        return false;
    }

    var i = 0;
    var codes = {};
    $.each(selectedRows, function (k, v) {
        var idVars = v.DT_RowId.split('-');
        codes['code_' + i] = idVars[0] + '-' + idVars[1];
        i++;
    });

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    $.ajax({
        url: '/blueseal/xhr/NamesManager',
        method: 'GET',
        dataType: 'JSON',
        data: codes
    }).done(function (res) {

        header.html('Unione Nomi');
        var bodyContent = '<div style="min-height: 250px"><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productDetailId" id="productDetailId"><option value=""></option></select></div>';
        bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
        bodyContent += '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="">';
        body.html(bodyContent);
        $('#productDetailId').selectize({
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            options: res,
            create: false,

            render: {
                option: function (item, escape) {
                    return '<div>' +
                        escape(item.name) +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 >= query.length) {
                    return callback();
                }
                var search = codes.slice();
                search['search'] = query;
                $.ajax({
                    url: '/blueseal/xhr/NamesManager',
                    type: 'GET',
                    data: search,
                    dataType: 'json',
                    error: function () {
                        callback();
                    },
                    success: function (res) {
                        callback(res);
                    }
                });
            }
        });

        $('#productDetailId').selectize()[0].selectize.setValue(0);

        var detName = $('#productDetailId option:selected').text(); //.split('(')[0];
        $('#productDetailName').val(detName);

        $(bsModal).find('table').addClass('table');
        $('#productDetailId').change(function () {
            var detName = $('#productDetailId option:selected').text(); //.split('(')[0];
            $('#productDetailName').val(detName);
        });
        cancelButton.html("Annulla");
        cancelButton.show();

        bsModal.modal('show');

        okButton.html(result.okButtonLabel).off().on('click', function (e) {
            var selected = $("#productDetailId").val();
            var name = $("#productDetailName").val();

            var oldCodes = [];

            body.html(loader);
            Pace.ignore(function () {
                body.html('');
                $.ajax({
                    url: "/blueseal/xhr/NamesManager",
                    type: "POST",
                    data: {
                        action: "mergeByProducts",
                        newName: name,
                        oldCodes: codes
                    }
                }).done(function (content) {
                    body.html(content);
                    okButton.html('Ok');
                    okButton.off().on('click', function () {
                        bsModal.modal('hide');
                        dataTable.ajax.reload(null, false);
                    });
                }).fail(function (content, a, b) {
                    body.html("Modifica non eseguita");
                    okButton.html('Ok');
                    okButton.off().on('click', function () {
                        bsModal.modal('hide');
                    });
                });
            });
        });
        bsModal.modal();
    });
});