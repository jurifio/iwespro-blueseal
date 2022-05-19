window.buttonSetup = {
    tag:"a",
    icon:"fa-sun-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-generate-indexproduct",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Genera la tabella prodotti veloce",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-generate-indexproduct', function () {

    let bsModal = new $.bsModal("Indice", {
        body: `<p>Genera la tabella Prodotti Veloce?</p>`
    });

        let products=1

        const data = {
            products: products
        };

        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/GenerateProductSuperFastListAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody('generazione eseguita con successo');
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });


});