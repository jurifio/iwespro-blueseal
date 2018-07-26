window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "allShops",
    event: "bs-update-from-db",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiorna la tabella degli utenti esterni (PER NEWSLETTER)",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-update-from-db', function (e, element, button) {


    let bsModal = new $.bsModal('SELEZIONA LO SHOP', {
        body: `<p>Seleziona lo shop da cui prelevare i dati</p>
                <select id="newsletterShopId">
                <option disabled selected value>Seleziona uno shop</option>
                </select>`
    });

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'NewsletterShop',
        },
        dataType: 'json'
    }).done(function (newsletterShops) {
        let selectShop = $('#newsletterShopId');
        if(typeof (selectShop[0].selectize) != 'undefined') selectShop[0].selectize.destroy();
        selectShop.selectize({
            valueField: 'id',
            labelField: 'name',
            options: newsletterShops
        });
    });


    bsModal.setOkEvent(function () {

        $.ajax({
            method: "post",
            url: "/blueseal/xhr/UpdateExternalUsersTable",
            data: {
                newsletterShopId: $('#newsletterShopId').val()
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });
});
