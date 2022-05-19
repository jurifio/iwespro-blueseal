window.buttonSetup = {
    tag:"a",
    icon:"fa-print",
    permission:"/admin/product/edit||worker",
    event:"bs-friend-order-invoice-download",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Stampa il DDT",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-friend-order-invoice-download', function () {
    let datatable = $('.table').DataTable();
    let selectedRows = datatable.rows('.selected').data();
    let selectedRowsCount = selectedRows.length;


    if (1 != selectedRowsCount) {
        modal = new $.bsModal(
            'Puoi stampare una riga alla volta',
            { body: '<p><strong>Attenzione:</strong></p><p>seleziona una singola riga.</p>'}
        );
        return false;
    }
    let i = 0;
    let row = '';
    let shootingId = '';
    $.each(selectedRows, function (k, v) {
        row = v.id;
        shootingId = v.row_id;
    });


    let bsModal = new $.bsModal('Aggiungi prodotti in shooting', {
        body: '<p>Vuoi stampare il ddt?</p>' +
            '<p id="returned"></p>'
    });


    const data = {
        shootingId: shootingId
    };
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductShootingPrintDdtAjaxController',
        data: data
    }).done(function (res) {
        let resp = JSON.parse(res);
        $('#returned').text(resp.message);
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            shootingId: shootingId
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductShootingPrintDdtAjaxController',
            data: data
        }).done(function (res) {
            if(res === "yes"){
                bsModal.writeBody("PDF stampato");
                window.open("/blueseal/download-invoice/" + row, '_blank');
            } else if(res === "no"){
                bsModal.writeBody("Nessun pdf associato. Devi creare un DDT prima di poterlo stampare.");
            }
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

});