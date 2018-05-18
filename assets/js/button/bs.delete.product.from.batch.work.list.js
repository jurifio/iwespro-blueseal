window.buttonSetup = {
    tag: "a",
    icon: "fa-window-close",
    permission: "/admin/product/edit&&allShops",
    event: "bs-delete-product-from-batch-work-list",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Elimina un prodotto da un lotto",
    placement: "bottom"
};

$(document).on('bs-delete-product-from-batch-work-list', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();


    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Puoi disassociare un prodotto alla volta"
        }).open();
        return false;
    }


    let prod = [] ;
    prod.push(selectedRows[0].DT_RowId);

    let pBatch = selectedRows[0].productBatchNumber.split(', ');

    if(pBatch[0] === ""){
        new Alert({
            type: "warning",
            message: "Il prodotto non è associato a nessun lotto"
        }).open();
        return false;
    } else {
        pBatch.splice(-1,1);
    }



    let bsModal = new $.bsModal('Elimina i prodotti da un lotto', {
        body: `<p>Sicuro di voler eliminare questi prodotti dal lotto?</p>
                <select id="selectBatchId">
                <option disabled selected value>Seleziona un lotto</option>
                ${pBatch.map((item, i) => `
                 <option value="${item}">${item}</option>
                `)}
                </select>
                `
    });



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let emptyBatch = "";
        let batchId = $('#selectBatchId').val();
        //vedo se è vuoto
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'ProductBatch',
                condition: {
                    id: batchId
                },
            },
            dataType: 'json'
        }).done(function (batch) {
            emptyBatch = (!(batch[0].contractDetailsId) ? 'empty' : 'full');

            const data = {
                products: prod,
                productBatchId: batchId,
                emptyBatch: emptyBatch
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductBatchManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

    });

    });