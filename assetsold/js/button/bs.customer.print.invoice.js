window.buttonSetup = {
    tag:"a",
    icon:"fa-file-o fa-archive",
    permission:"/admin/order/list&&allShops",
    event:"bs-print-invoice-customer",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea (e stampa) la fattura per il cliente",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-print-invoice-customer', function () {
    let order = (new URL(document.location)).searchParams.get("order");

    if (order == null){
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        }

        order = selectedRows[0].DT_RowId;
    }

    let bsModal = new $.bsModal('Stampa ordine', {
        body: `<div class="form-group form-group-default required">
        <label for="destination">Seleziona la destinazione</label>
        <select id="destination" name="destination">
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="it">Italia</option>
        <option value="ex">Estero</option>
        </select> 
        </div><div id="otherOptions"></div>`
    });


    $('#destination').change(function () {

        let destination = $('#destination').val();
        let html = "";

        if (destination === 'it') {
            html = `<a href="/blueseal/xhr/InvoiceAjaxController?orderId=${order}" target="_blank" class="btn btn-success">Stampa ordine</a>`
        } else {
            html = `<div class="form-group form-group-default required">
        <label for="esenzCode">Inserisci il codice d'esenzione</label>
        <input type="text" id="esenzCode" name="esenzCode">
        </div>
        <button id="exPrintOrder" class="btn btn-success">Stampa ordine</button>`;
        }

        $('#otherOptions').empty().append(html);

        $('#exPrintOrder').on('click', function () {
            let extCode = $('#esenzCode').val();

            if(extCode){
                let extUrl = `/blueseal/xhr/InvoiceAjaxController?orderId=${order}&extCode=${extCode}`;
                window.open(extUrl, "_blank");
            } else {
                $('#otherOptions').empty().append("Devi inserire un codice di esenzione");
            }

        });
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        bsModal.hide();
    });
});