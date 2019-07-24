window.buttonSetup = {
    tag:"a",
    icon:"fa-shower",
    permission:"/admin/product/edit",
    event:"bs-product-eantoexternal-align",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Allinea ",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-eantoexternal-align', function () {



    let bsModal = new $.bsModal('Esegui l\'allineamento tra Ean produttore e Ean Picky', {
        body: `<p>Allineamento</p>`
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        var send="";


        const data = {
            send: send
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/AlignEanInternalToExternalAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
        });
    });
});