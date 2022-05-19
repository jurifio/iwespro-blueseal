window.buttonSetup = {
    tag:"a",
    icon:"fa-check-square-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-processingUpdate",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Stato di lavorazione ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-processingUpdate', function () {

    let datatable = $('.table').DataTable();

    let selectedRows = datatable.rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (1 > selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    let i = 0;
    let row = [];

    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        i++;
    });

    modal = new $.bsModal('Modifica lo stato di lavorazione del prodotto');

    $.ajax({
        url: '/blueseal/xhr/ProductProcessingUpdate',
        method: 'GET',
        data: {}
    }).done(function(res){
        res = JSON.parse(res);
        let body = '<div class="form-group">' +
                '<select class="form-control processing-options">[options]</select>' +
            '</div>';
        let options = '<option value="">Seleziona lo stato da assegnare</option>';
        for(i in res) {
            options+= '<option value="' + res[i] + '">' + res[i] + '</option>';
        }

        body = body.replace('[options]', options);
        modal.writeBody(body);

        modal.setOkEvent(function(){
            let val = $('.processing-options option:selected').val();
            $.ajax({
                url: '/blueseal/xhr/ProductProcessingUpdate',
                method: 'POST',
                data: {processing: val, rows: row},
            }).done(function(res){
                modal.writeBody(res);
                modal.setOkEvent(function(){
                    datatable.ajax.reload(false);
                    modal.hide();
                });
            }).fail(function(res){
                modal.writeBody('Oops! c\'è stato un problema! Contatta un amministratore');
                console.log(res);
            });
        });
    }).fail(function(res){
        modal.writeBody('Oops! c\'è stato un problema! Contatta un amministratore');
        console.log(res);
    });

});
