window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-square",
    permission:"/admin/product/list",
    event:"bs-create-ddt",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea ddt",
    placement:"bottom"
};

$(document).on('bs-create-ddt', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi creare un solo ddt alla volta"
        }).open();
        return false;
    } else if (selectedRowsCount < 1){
        new Alert({
            type: "warning",
            message: "Non hai selezionato nessuna riga da cui creare il ddt"
        }).open();
        return false;
    } else if(selectedRowsCount === 1){

        let shootingId = selectedRows[0].row_id;

        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Stampare il DDT per lo shooting con codice ' + shootingId + '?</p>'+
            '<div class="form-group form-group-default required">' +
            '<label for="carrier">Corriere</label>' +
            '<select id="carrier" name="carrier"></select>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="coll">Numero colli</label>' +
            '<input autocomplete="on" type="text" id="coll" ' +
            'placeholder="Numero di colli" class="form-control" name="coll" required="required">' +
            '</div>'
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier'
            },
            dataType: 'json'
        }).done(function (res) {
            let select = $('#carrier');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'name',
                labelField: 'name',
                options: res,
            });
        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                coll: $('#coll').val(),
                carrier: $('#carrier').val(),
                shooting: shootingId
                    };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/CreateDdtAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    }


});
