$(document).on('', function () {
    "use strict";
    $('ul.breadcrumb').append($('<li>test</li>'))
});

$(document).on('bs.marketplace.filter', function () {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Filtra Tabella');

    body.html('<img src="/assets/img/ajax-loader.gif" />');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductManageController',
            type: "get",
        }).done(function (response) {
            var accounts = JSON.parse(response);
            var html = '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="accountFilterId">Marketplace Account</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="accountId" id="accountFilterId" required>' +
                '<option value=""></option>';
            for (let account of accounts) {
                html += '<option value="' + account.id + '" data-has-cpc="' + account.cpc + '" data-modifier="' + account.modifier + '">' + account.marketplace + ' - ' + account.name + '</option>';
            }
            html += '</select>';
            html += '</div>';

            body.html($(html));

            okButton.off().on('click', function () {
                window.location.href = '/blueseal/prodotti/marketplace/account/' + $('#accountFilterId').val();
            });
        });
    });

    bsModal.modal();
});

$(document).on('bs.ean.newRange', function (e, element, button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Inserisci Range Ean');

    body.html('<div>Immetti Codici di 12 caratteri per Inizio e Fine</div>' +
        '<div class="form-group form-group-default">' +
        '<label for="start">Inizio</label>' +
        '<input type="text" minlength="12" maxlength="12" id="start">' +
        '<label for="end">Fine</label>' +
        '<input type="text" minlength="12" maxlength="12" id="end">' +
        '</div>');
    okButton.off().on('click', function () {
        var start = $('#start').val();
        var end = $('#end').val();
        if (start.length != 12 || end.length != 12) {
            new Alert({
                type: "warning",
                message: "Devi immettere codici di 12 caratteri"
            }).open();
        } else {
            Pace.ignore(function () {
                $.ajax({
                    url: '/blueseal/xhr/GenerateEanCodes',
                    type: "POST",
                    data: {
                        start: start,
                        end: end
                    }
                }).done(function (response) {
                    body.html('Inseriti ' + response + ' nuovi Ean');
                    cancelButton.off().hide();
                });

                body.html('<img src="/assets/img/ajax-loader.gif" />');
            });
        }

    });

    bsModal.modal();
});
$(document).on('bs.product.ean', function (e, element, button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Associa Ean Prodotti');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti a cui associare Ean"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });

    body.html('Vuoi associare nuovi ean ai prodotti selezionati?');

    okButton.off().on('click', function () {
        bsModal.modal('hide');
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/AssignEanToSkus',
                type: "POST",
                data: {
                    rows: getVarsArray
                }
            }).done(function (resoult) {
                resoult = JSON.parse(resoult);
                new Alert({
                    type: "success",
                    message: "Associati " + resoult.skus + " nuovi Ean per " + resoult.products + " prodotti"
                }).open();
            }).fail(function (resoult) {
                new Alert({
                    type: "warning",
                    message: "Errore: " + resoult
                }).open();
            });
        });
    });
    bsModal.modal();
});

$(document).on('bs.product.retry', function (e, element, button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Pubblica Prodotti');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli inviare "
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });


    body.html('<img src="/assets/img/ajax-loader.gif" />');

    Pace.ignore(function () {

        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductManageController',
            type: "PUT",
            data: {
                rows: getVarsArray
            }
        }).done(function () {

        }).always(function () {
            bsModal.modal('hide');
            $('.table').DataTable().ajax.reload();
        });
    });


    bsModal.modal();
});


$(document).on('bs.dateinput.load', function (a, b) {
    var table = $('table.table');
    var dataTable = table.DataTable();
    var that = $('#bsButton_'+b.id);
    that.on('apply.daterangepicker', function (ev, picker) {
        var controller = dataTable.ajax.url();
        controller = $.addGetParam(controller, 'startDate', picker.startDate.format('YYYY-MM-DD'));
        controller = $.addGetParam(controller, 'endDate', picker.startDate.format('YYYY-MM-DD'));
        table.DataTable().ajax.url(controller);
        table.data('controller', controller);
        table.DataTable().search("").draw();
    });

    that.on('cancel.daterangepicker', function (ev, picker) {
        var controller = table.data('controller');
        var cicc = $.decodeGetStringFromUrl(controller);
        delete cicc.startDate;
        delete cicc.endDate;
        controller = $.encodeGetString(cicc);
        table.data('controller', controller);
        table.DataTable().search("").draw();
    });

    var options = {
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: "Cancella",
            applyLabel: "Applica"
        },
        ranges: {
            'Oggi': [moment(), moment()],
            'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimi 7 Giorni': [moment().subtract(6, 'days'), moment()],
            'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
            'Questo Mese': [moment().startOf('month'), moment().endOf('month')],
            'Scorso Mese': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        alwaysShowCalendars: true,
        autoUpdateInput: false,
        drops: "down",
        parentEl: "div.panel-body"
    };
    that.daterangepicker(options);
});
