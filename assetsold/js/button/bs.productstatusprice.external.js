window.buttonSetup = {
    tag:"a",
    icon:"fa-list",
    permission:"/admin/product/edit&&allShops",
    event:"bs.productstatusprice.external",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa massivamente il tag 'New Season'",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.productstatusprice.external', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un prodotto alla Volta"
        }).open();
        return false;
    }
     let body='';
    let modal = new $.bsModal(
        'Visualizza Stato e  Prezzi sui front',
        {
            body: body,
            isCancelButton: true
        }
    );


        modal.addClass('modal-wide');
        modal.addClass('modal-high');


    //modal.body.css('minHeight', '350px');
    modal.show();



       let productId = selectedRows[0].DT_RowId.split('-')[0];
        let productVariantId = selectedRows[0].DT_RowId.split('-')[1];



    $.ajax({
        url: '/blueseal/xhr/ShopHasProductPriceAjaxController',
        method: 'get',
        data: {
            productId: productId,
            productVariantId: productVariantId

        },
        dataType: 'json'
    }).done(function (res) {
let result='<div class="row"><div class="col-md-2">Shop</div><div class="col-md-2">Stato</div><div class="col-md-2">prezzo di Vendita</div><div class="col-md-2">prezzo in Sconto</div><div class="col-md-2">prezzo di Costo</div><div class="col-md-2">in Saldo</div></div>';
        $.each(res, function (k, v) {
           result=result+'<div class="row"><div class="col-md-2">'+v.nameShop+'</div><div class="col-md-2">'+v.productStatus+'</div><div class="col-md-2">'+v.price+'</div><div class="col-md-2">'+v.salePrice+'</div><div class="col-md-2">'+v.value+'</div><div class="col-md-2">'+v.isOnSale+'</div></div>';
        });
        modal.writeBody(result);
    }).fail(function () {
        modal.writeBody('Errore grave');
    }).always(function () {
        modal.setOkEvent(function () {
            modal.hide();
        });
        modal.showOkBtn();
    });

});