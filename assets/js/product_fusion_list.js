$(document).on('bs.product.merge', function(){
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 2) {
        new Alert({
            type: "warning",
            message: "Devi selezionare esattamente due prodotti"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('__');
        row[i].id = idsVars[1];
        row[i].productVariantId = idsVars[2];
        row[i].name = v.brand;
        row[i].cpf = v.cpf;
        i++;
        getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    header.html('Fondi 2 prodotti');

    body.css("text-align", 'left');

    $.ajax({
        url: '/blueseal/xhr/ProductMerge',
        type: 'GET',
        data: {rows: row}
    }).done(function(res){
        res = JSON.parse(res);
        var error = '';

        //controllo se entrambi i prodotti hanno ordini
        var countOrderedProducts = 0;
        /*$.each(res.rows, function(k, v){
            if (v.areOrders) countOrderedProducts++;
        });

        if (1 < countOrderedProducts) {
            error += "due o più prodotti selezionati hanno associati degli ordini. La fusione è impraticabile.";
        }*/

        if (false === res.sizeGroupCompatibility) {
            error += "i due prodotti sono associati con gruppi taglia incompatibili."
        }

        if ('' !== error) {
            body.html(':-( Non posso procedere alla fusione:<br />' + error);
            cancelButton.hide();
            okButton.html('Ok').off().on("click", function () {
                bsModal.modal("hide");
            });
        } else {
            var bodyMsg = '<p>Seleziona il prodotto che rimarrà in catalogo:</p><form>';
            var radio = '';
            var selected = false;
            $.each(res.rows, function(k, v){
                radio += '<input type="radio" name="choosen" value="' + k + '" ';
                if ((0 < countOrderedProducts) && (0 == v['areOrders'])) {
                    radio += 'disabled="disabled"'
                } else {
                    if (false == selected) {
                        radio += 'checked';
                        selected = true;
                    }
                }
                radio += ' /> ' + v['id'] + '-' + v['productVariantId'] + ' ' + v['name'] + ' ' + v['cpf'] + '<br />';
            });
            bodyMsg += radio;
            bodyMsg += '</form><p>Se uno dei prodotti è stato acquistato sarà la scelta obbligata</p>';
            body.html(bodyMsg);
            cancelButton.html("Annulla").show().on('click', function () {
                bsModal.hide();
            });
            okButton.html("Fondi").off().on('click', function () {
                var choosen = $('input[name="choosen"]').val();
                body.html("Pensaci un momento. L'azione non è reversibile!");
                cancelButton.html("Ci ho ripensato");
                okButton.html("Fondi!").off().on('click', function () {
                    $.ajax({
                        url: '/blueseal/xhr/ProductMerge',
                        type: 'POST',
                        data: {action: "merge", rows: row, choosen: choosen}
                    }).done(function(res){
                        body.html(res);
                        cancelButton.hide();
                        okButton.html("Ok").off().on('click', function () {
                            bsModal.modal("hide");
                            dataTable.ajax.reload();
                        });
                    });
                });
            });
        }
    });
    bsModal.modal('show');
});