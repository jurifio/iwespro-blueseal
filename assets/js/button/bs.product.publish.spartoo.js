window.buttonSetup = {
    tag:"a",
    icon:"fa-square",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-publish-spartoo",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Pubblica su Spartoo!",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-publish-Spartoo', function (e, element, button) {
    let bsModal = new $.bsModal('Pubblica', {
        body: '<p>Confermi la Pubblicazione dei Prodotti in Spartoo ?</p>'
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data={ id: 1
        }
        $.ajax({
            url: '/blueseal/xhr/SpartooAddProductAjaxController',
            method: 'post',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });

});

