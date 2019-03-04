window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionaryimagesize-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Inserimento parametri Immagini da elaborare",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionaryimagesize-insert', function () {
    let bsModal = new $.bsModal('Inserimento parametri Immagini da elaborare', {

        body: `
        <div class="form-group form-group-default required">' 
        '<label for="shopId">Seleziona il Friend</label>' 
        '<select id="shopId" name="shopId">' 
        '<option disabled selected value>Seleziona un\\'opzione</option>' 
        '</select>' 
        '</div>'  '<div id="otherOptions"></div>

         <div><p>Conferma'
        '</div>`
    });
    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'shop'
        },
        dataType: 'json'
    }).done(function (res2) {
        let select = $('#nameShop');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'fromEmailAddressId',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
const data={
    id: '1',
        };
        $.ajax({
            method: 'POST',
            url: "/blueseal/xhr/PrestashopAlignImage",
            data:data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
               // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });
});