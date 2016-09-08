window.buttonSetup = {
    tag:"a",
    icon:"fa-tasks",
    permission:"/admin/product/edit",
    event:"bs.product.mergedetails",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Copia dettagli",
    placement:"bottom",
    toggle:"modal"
};

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
