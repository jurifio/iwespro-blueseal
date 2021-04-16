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
    var code = selectedRows[0].code;
    var seo = selectedRows[0].seo;
    var remoteShopId=  selectedRows[0].remoteShopId;
    var remoteId=selectedRows[0].remoteId;
    var selAPP = '';
    var selLOOK = '';
    var selCOLOUR = '';
    switch (code) {
        case 'APP':
            selAPP = 'checked="checked"';
            selLOOK = '';
            selCOLOUR = '';
            break;
        case 'LOOK':
            selAPP = '';
            selLOOK = 'checked="checked"';
            selCOLOUR = '';
            break;
        case 'COLOUR':
            selAPP = '';
            selLOOK = '';
            selCOLOUR = 'checked="checked"';
            break;
    }

    let bsModal = new $.bsModal('Modifica un Tema di Correlazione fra Prodotti', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
        <label>Nome Correlazione</label>
        <input type="text" id="nameCorrelation" name="nameCorrelation" value="` + name + `"/>
                </div>
                </div>
                 <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">seleziona lo Shop Di Destinazione</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize"></select>
                                                
                                    </div>
                </div>
                <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="code">seleziona il Tipo di Correlazione</label>
                                        <select id="code" name="code"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option ` + selAPP + `  value="APP">Potrebbe Piacerti Anche</option>
                                            <option ` + selLOOK + `  value="LOOK">look</option>
                                            <option  ` + selCOLOUR + ` value="COLOUR">Colore</option>
                                        </select>
                                    </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" name="description" id="description">
                                                  ` + description + `</textarea>
                                    </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  >` + note + `</textarea>
                                    </div>
                </div>
                 <div class="row">
                <div class="form-group form-group-default">
                                        <label for="seo">seo</label>
                                        <textarea class="form-control" name="seo" id="seo"
                                                  >` + seo + `</textarea>
                                    </div>
                </div>
                `
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            condition: {hasEcommerce: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            onInitialize: function () {
                var selectize = this;
                selectize.setValue(remoteShopId);
            }
        });

    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        const data = {
            id: id,
            name: $('#nameCorrelation').val(),
            description: $('#description').val(),
            code:$('#code').val(),
            seo:$('#seo').val(),
            note: $('#note').val(),
            shopId:$('#shopId').val(),
            remoteId:remoteId

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