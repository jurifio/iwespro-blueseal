window.buttonSetup = {
    tag: "a",
    icon: "fa-edit",
    permission: "/admin/product/edit",
    event: "bs-product-correlation.modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica  Correlazione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-correlation.modify', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
    var id =selectedRows[0].DT_RowId;
    var name=selectedRows[0].name;
    var description=selectedRows[0].description;
    var note=selectedRows[0].note;

    let bsModal = new $.bsModal('Modifica un Tema di Correlazione fra Prodotti', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
        <label>Nome Correlazione</label>
        <input type="text" id="nameCorrelation" name="nameCorrelation" value="`+name+`"/>
                </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" name="description" id="description">
                                                  `+description+`</textarea>
                                    </div>
                </div>
                <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  >`+note+`</textarea>
                                    </div>
                </div>
                `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            id:id,
            name: $('#nameCorrelation').val(),
            description: $('#description').val(),
            note:$('#note').val(),
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ProductCorrelationAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $('.table').DataTable().ajax.reload();
            });
        });
    });
});