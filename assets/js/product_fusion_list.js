$(document).on('bs.product.merge', function(){
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
        row[i].name = v.name;
        row[i].cpf = v.cpf;
        i++;
        getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    header.html('Fondi 2 prodotti');

    body.css("text-align", 'left');
    var bodyMsg = '<form>';
    var radio = '';
    $.ajax({
        url: '/blueseal/xhr/ProductMerge',
        type: 'GET',
        data: {rows: row}
    }).done(function(res){
        res = JSON.parse(res);

        //controllo se entrambi i prodotti hanno ordini
        countOrderedProducts = 0;
        $.each(res.rows, function(k, v){

        });

        var notPermitted = false;
        if (false === res.sizeGroupCompatibility) {
            body.html('I gruppi taglia non coincidono, la fusione non può essere portata a termine');
            cancelButton.hide();
            okButton.off().on("click", function(){
                bsModal.modal("hide");
            });
        } else {

        }

        bodyMsg += radio;
        bodyMsg += '</form>';
        body.html(bodyMsg);
        cancelButton.html("Annulla").show().on('click', function(){
            bsModal.hide();
        });
        okButton.html("Fondi").off().on('click', function(){
            var choosen = $('input[name="mainProd"]').val();
            body.html("Pensaci un momento. L'azione non è reversibile!");
            cancelButton.html("Ci ho ripensato");
            okButton.html("Mi assumo le mie responsabilità davanti a Dio").off().on('click', function(){
                $.ajax({
                    url: '/blueseal/xhr/ProductMerge',
                    type: 'POST',
                    data: {action: "merge", rows: row, choosen: choosen}
                });
            });
        }); 
    });
    bsModal.modal('show');
});