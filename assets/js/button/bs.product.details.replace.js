window.buttonSetup = {
    tag: "a",
    icon: "fa-list-ul",
    permission: "/admin/product/edit",
    event: "bs.product.details.replace",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Sostituisci Dettagli",
    placement: "bottom"
};

$(document).on('bs.product.details.replace', function (e, element, button) {
    let productsId = $.getDataTableSelectedRowsData('.table','DT_RowId',1);
    if(productsId == false) return false;
    let modal = new $.bsModal(
        'Sovrascrivi Dettagli',
        {
            body: '<div class="alert alert-danger modal-alert" style="display: none">Inserire un dettaglio per ogni riga</div>' +
            '<form id="detailsAdd">' +
                '<div class="form-group">' +
                    '<label>Dettagli</label>' +
                    '<textarea class="form-control" id="detailsText" name="detailsText" />' +
                '</div>'+
            '</form>',
            okLabel: 'Sovrascrivi'
        }
    );

    modal.setOkEvent(function() {
        "use strict";

        let value = $('#detailsAdd #detailsText').val();
        modal.hide();
        $.ajax({
            method:"put",
            url:"/blueseal/DetailsReplace",
            data:{
                newDetails: value,
                productsId: productsId
            }
        }).done(function(res) {
            new Alert({
                type: "success",
                message: "Aggiornati "+productsId.length+" elementi"
            }).open();
        }).fail(function(res) {
            new Alert({
                type: "warning",
                message: "Errore nell'aggiornare "+productsId.length+" elementi"
            }).open();
        });
    })

});