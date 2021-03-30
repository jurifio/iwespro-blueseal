window.buttonSetup = {
    tag:"a",
    icon:"fa-amazon",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-publish-amazon",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Pubblica su amazon!",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-publish-amazon', function (e, element, button) {
    let bsModal = new $.bsModal('Pubblica', {
        body: '<p>Confermi la Pubblicazione dei Prodotti in Amazon ?</p>'
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data={ id: 1
        }
        $.ajax({
            url: '/blueseal/xhr/AmazonNewAddProductAjaxController',
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

