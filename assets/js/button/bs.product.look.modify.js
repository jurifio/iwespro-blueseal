window.buttonSetup = {
    tag: "a",
    icon: "fa-edit",
    permission: "/admin/product/edit",
    event: "bs-product-look.modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica  Look",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-look.modify', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
    var id = selectedRows[0].DT_RowId;
    var name = selectedRows[0].name;
    var description = selectedRows[0].description;
    var note = selectedRows[0].note;
    var discountActive=selectedRows[0].discountActive;
    var typeDiscount=selectedRows[0].typeDiscount;
    var seldaYes='';
    var seldaNo='';
    var selTypeDiscountP='';
    var selTypeDiscountF='';

    switch (discountActive) {
        case 'no':
             seldaYes='';
             seldaNo='checked="checked"';
            break;
        case 'si':
            seldaYes='checked="checked"';
            seldaNo='';
            break;
    }
    switch (typeDiscount) {
        case '1':
            selTypeDiscountP='checked="checked"';
            selTypeDiscountF='';
            break;
        case '2':
            selTypeDiscountP='';
            selTypeDiscountF='checked="checked"';
            break;
    }

    let bsModal = new $.bsModal('Modifica un Look fra Prodotti', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
        <label for="Nome Look"</label>
        <input type="text" id="nameLook" name="nameLook" value="` + name + `"/>
                </div>
                </div>
               
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" name="description" id="description">
                                                  ` + description + `</textarea>
                                    </div>
                </div>
                <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  >` + note + `</textarea>
                                    </div>
                </div>
                 <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="discountActive">Sconto Attivo</label>
                                        <select id="discountActive" name="discountActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option ` + seldaYes + ` value="1">Sì</option>
                                            <option ` + seldaNo + ` value="0">no</option>
                                        </select>
                                    </div>
                </div>
                <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeDiscount">tipo di Sconto</label>
                                        <select id="typeDiscount" name="typeDiscount"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option ` + selTypeDiscountP + `  value="1">Percentuale</option>
                                            <option ` + selTypeDiscountF + `  value="2">Fisso</option>
                                        </select>
                                    </div>
                </div>
               
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="amount">Valore</label>
                                        <input type="text" id="amount" name="amount" value="` + amount `"/>
                                    </div>
                </div>
              
                `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        const data = {
            id: id,
            name: $('#nameCorrelation').val(),
            description: $('#description').val(),
            note: $('#note').val(),
            typeDiscount:$('#typeDiscount').val(),
            discountActive:$('#discountActive').val(),
            amount:$('#amount').val()

        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ProductLookAjaxController',
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