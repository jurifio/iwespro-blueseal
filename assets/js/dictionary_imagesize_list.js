(function ($) {

    $(document).ready(function () {

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterShop'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select = $('#nameShop');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });

    });
})(jQuery);

window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionaryimagesize-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Gestione parametri foto Prodotti Friend",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.dictionaryimagesize.insert', function () {
    let bsModal = new $.bsModal('inserisci i parametri delle immagini prodotti del  Friend', {
        body: '<div><p>Premere ok per Salvare i Parametri' +
        '</div>'+
        '<div class="form-group form-group-default required">' +
        '<label for="shopId">Seleziona il Friend</label>' +
        '<select id="shopId" name="shopId">' +
        '<option disabled selected value>Seleziona un \'opzione</option>'+
        '</select>' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            dateCampaignStart: $('#dateCampaignStart').val(),
            dateCampaignFinish: $('#dateCampaignFinish').val(),
            nameShop: $('#nameShop').val(),
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterCampaignManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});




