window.buttonSetup = {
    tag:"a",
    icon:"fa-puzzle-piece",
    permission:"/admin/product/edit",
    event:"bs-product-ean-align",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Allinea Etichette etichette",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-ean-align', function () {



    let bsModal = new $.bsModal('Esegui l\'allineamento tra Ean produttore e Ean Picky', {
        body: `<p>Allineamento</p>
                <div id="size"></div>
                `
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        var send="";


        const data = {
            send: send
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/AlignEanExternalToInternalAjaxController',
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