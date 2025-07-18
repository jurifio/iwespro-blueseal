window.buttonSetup = {
    tag:"a",
    icon:"fa-magnet",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-namesMerge",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Copia i nomi dei prodotti",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-namesMerge', function () {

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

    var codes = {};
    var i = 0;
    $.each(selectedRows, function (k, v) {
        codes['codes_' + i] = v.DT_RowId;
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
        var prodNameId = $ ('#productDetailId');
        prodNameId.selectize({
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
                var search = codes;
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
                        console.log(res);
                        res.push({name: search['search']});
                        callback(res);
                    }
                });
            }
        });


        prodNameId.selectize()[0].selectize.setValue(0);
        var prodName = $ ('#productDetailName');

        var detName = prodNameId.find ('option:selected').text();
        prodName.val (detName);

        prodNameId.on ('change', function(){
            detName = prodNameId.find ('option:selected').text();
            prodName.val (detName);
        });

        $(bsModal).find('table').addClass('table');
        cancelButton.html("Annulla");
        cancelButton.show();

        bsModal.modal('show');

        okButton.html(result.okButtonLabel).off().on('click', function (e) {
            var selected = $("#productDetailName").val();

            var oldCodes = [];

            body.html(loader);
            Pace.ignore(function () {
                body.html('');
                delete codes['search'];
                $.ajax({
                    url: "/blueseal/xhr/NamesManager",
                    type: "POST",
                    data: {
                        action: "mergeByProducts",
                        insertNameIfNew: "r'n'r!",
                        newName: selected,
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