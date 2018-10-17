window.buttonSetup = {
    tag:"a",
    icon:"fa-file-text-o",
    permission:"/admin/product/edit",
    event:"bs-ean-update",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci etichette",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-ean-update', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }

    let bsModal = new $.bsModal('Assegna un ean', {
        body: `<p>Inserisci l'ean per il prodotto con codice: ${selectedRows[0].DT_RowId}</p>
                <div id="size"></div>
                `
    });

    let insert = '';
    const dataG = {
      p: selectedRows[0].DT_RowId
    };
    $.ajax({
        method: 'get',
        url: '/blueseal/xhr/ManageProductSkuEan',
        dataType: 'JSON',
        data: dataG
    }).done(function (data) {
        $.each(data, function (k, v) {
            insert += `<div><label style="margin-right: 10px" for="ean">Taglia: ${v['sizeName']}</label><input type="text" data-sizeId="${v['sizeId']}" value="${v['ean']}" class="ean"></div>`;
        });

        $("#size").append(insert);
    });




    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let sizeV = [];

        $('.ean').each(function () {
            sizeV.push({
                size: $(this).attr('data-sizeId'),
                val: $(this).val()
            });
        });


        const data = {
            p: selectedRows[0].DT_RowId,
            size: sizeV
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ManageProductSkuEan',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
        });
    });
});