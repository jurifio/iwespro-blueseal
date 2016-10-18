$(document).on('bs.manage.names', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');


    header.html('Riordina nomi');
    body.html('Riordino dei nomi. Eliminazione dei duplicati');
    $.ajax({
        url: "/blueseal/xhr/NamesManager",
        type: "POST",
        data: {action: "clean"}
    }).done(function (result) {
        body.html(result);
        okButton.html('Ok').off().on('click', function () {
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
    }).fail(function (res, a, b) {
        console.log(res);
        console.log(a);
        console.log(b);
        body.html("OOPS! C'è stato un problemino");
        okButton.html('Ok').off().on('click', function () {
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
    });
});

$(document).on('bs.names.merge', function () {

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
    var row = [];
    var getVars = '';
    var oldNames = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        //row[i].id = v.DT_RowId.split('__')[1];
        row[i].name = v.name;
        oldNames.push(v.name);
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    header.html('Unione Nomi');
    var bodyContent = '<div style="min-height: 250px"><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productDetailId" id="productDetailId"><option value=""></option></select></div>';
    bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
    bodyContent += '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="">';
    body.html(bodyContent);
    $('#productDetailId').selectize({
        valueField: 'name',
        labelField: 'name',
        searchField: 'name',
        options: row,
        create: false,
        /*score: function(search) {
         var score = this.getScoreFunction(search);
         return function(item) {
         return score(item) * (1 + Math.min(item.watchers / 100, 1));
         };
         },*/
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
            $.ajax({
                url: '/blueseal/xhr/NamesManager',
                type: 'GET',
                data: "search=" + query,
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
    $('#productDetailId').selectize()[0].selectize.setValue(row[0].id);

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
        console.log(name);
        body.html(loader);
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/NamesManager",
                type: "POST",
                data: {
                    action: "merge",
                    newName: name,
                    oldNames: oldNames
                }
            }).done(function (content) {
                body.html(content);
                okButton.html('Ok');
                okButton.off().on('click', function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload(null,false);
                });
            }).fail(function (content, a, b) {
                console.log(content);
                console.log(a);
                console.log(b);
                body.html("Modifica non eseguita");
                okButton.html('Ok');
                okButton.off().on('click', function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload(null,false);
                });
            });
        });
    });
    bsModal.modal();
});

$(document).on('bs.names.products', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (1 != selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        row[i].name = v.name;
        i++;
    });
    
    header.html('Elenco dei prodotti (max 500)');
    $.ajax({
        url: "/blueseal/xhr/NamesProductAssociated",
        type: "GET",
        dataType: 'json',
        data: {search: row[0].name}
    }).done(function (result) {
        body.html('');
        body.css('max-height', '400px');
        body.css('overflow-y', 'auto');
        var bodyRes = '<p>' + result.langs + '</p>';
        bodyRes += '<ul>';
        $.each(result.products, function(k, v){
            bodyRes += '<li>' + v['link'] + ': ' + v['brand'] + ' - ' + v['season'] + '  <img style="width: 40px" src="' + v['pic'] + '" /></li>';
        });
        bodyRes += '</ul>';
        body.html(bodyRes);
        okButton.html('Ok').off().on('click', function () {
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
    }).fail(function (res, a, b) {
        console.log(res);
        console.log(a);
        console.log(b);
        body.html("OOPS! C'è stato un problemino!");
        okButton.html('Ok').off().on('click', function () {
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
    });
});