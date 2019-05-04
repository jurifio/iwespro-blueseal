window.buttonSetup = {
    tag:"a",
    icon:"fa-cloud-upload",
    permission:"/admin/order/list&&allShops",
    event:"bs-order-upload-document",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Carica un file relativo all'ordine",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-order-upload-document', function () {

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
    
    let bsModal = new $.bsModal('Carica file', {
        body:
        `<div class="form-group form-group-default required">
        <label>Tipo documento</label>
        <input type="text" id="documentType" name="documentType">
        <label>Carica file</label>
        <input type="file" id="docBin" name="docBin">
        </div>`
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        let docType = $('#documentType').val();
        let invoiceFile = $('#docBin').prop('files')[0];
        let data = new FormData();
        data.append('type', docType);
        data.append('file', invoiceFile);
        data.append('order', order);
        $.ajax({
            url: '/blueseal/xhr/UploadInvoiceDocument',
            cache: false,
            contentType: false,
            processData: false,
            method: 'post',
            dataType: 'json',
            data: data
        }).done(function (res) {
            bsModal.hide();
            if(res === 'ok'){
                new Alert({
                    type: "success",
                    message: "Documento inserito con successo"
                }).open();
            } else if (res === 'err'){
                new Alert({
                    type: "warning",
                    message: "Inserisci tutti i dati"
                }).open();
            } else if (res === 'err_grave'){
                new Alert({
                    type: "danger",
                    message: "Errore durante l'inserimento in database"
                }).open();
            }

        }).fail(function(res) {
            new Alert({
                type: "danger",
                message: "Errore grave"
            }).open();
        });

    });

});