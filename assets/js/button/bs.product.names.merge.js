window.buttonSetup = {
    tag:"a",
    icon:"fa-magnet",
    permission:"/admin/product/edit&&allShops",
    event:"bs.product.mergenames",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Copia i nomi dei prodotti",
    placement:"bottom",
    toggle:"modal"
};

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
