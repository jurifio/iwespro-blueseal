window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/edit&&allShops",
    event:"bs.order.addOrderLine",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi un prodotto all'ordine",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.order.addOrderLine', function () {
    var orderId = $.QueryString['order'];

    modal = new $.bsModal(
        'Aggiungi un prodotto all\'ordine',
        {
            isCancelButton: true,
        }
    );
    modal.disableOkButton();

    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'Order',
            condition: {id: orderId}
        }
    }).done(function(res){
        let body = '<div class="row">' +
                        '<div class="col-md-12">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="product-search">Prodotto</label>' +
                                    '<input id="product-search" class="form-control" placeholder="Cerca un prodotto"' +
                                    'name="product-search" required="required" />' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row row-borded order-line">' +
                        '<div class="col-xs-12">' +
                            '<h5>Risultati della ricerca:</h5>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-xs-12 search-results">' +
                        '</div>' +
                    '</div>';
        modal.writeBody(body);
        $(document).on('keypress', '#product-search', function (e) {
            elem = $(e.target);
            if (13 == e.charCode) {
                e.preventDefault();
            }
            let val = elem.val();
            modal.disableOkButton();
            if (3 < val.length) {
                let query = elem.val();
                $.ajax({
                    url: '/blueseal/xhr/GetProductByAnyString',
                    type: 'GET',
                    data: {
                        search: query,
                        onlyPublic: true,
                        limit: 50
                    },
                    dataType: 'json'
                }).done(function (res) {
                    let searchResults = $('.search-results');

                    let itemMock = '<div class="col-xs-4">' +
                        '<div id="product_code_{data.code}" data-code="{data.code}" data-public-url="{data.publicUrl}" class="product-selection-box">' +
                        '<img class="img-responsive thumbnail" src="{data.dummyUrl}" />' +
                        '<p class="product-brand" data-brand="{data.brand}"><span class="small">Brand: </span>{data.brand}</p>' +
                        '<p><span class="small">Code: </span>{data.code}</p>' +
                        '<p><span class="small">Cpf: </span>{data.cpf}</p>' +
                        '<p><span class="small">Taglie Disp.: </span>{data.sizes}</p>' +
                        '</div>' +
                        '</div>';

                    let hbody = '';
                    let obj;
                    for (let i in res) {
                        obj = res[i];
                        hbody += itemMock
                            .replaceAll('{data.dummyUrl}', obj.dummyUrl)
                            .replaceAll('{data.brand}', obj.brand)
                            .replaceAll('{data.code}', obj.code)
                            .replaceAll('{data.publicUrl}', obj.publicUrl)
                            .replaceAll('{data.cpf}', obj.cpfVar)
                            .replaceAll('{data.sizes}', obj.availableSizes.join(' - '));
                    }
                    searchResults.html(hbody);
                    searchResults.children().each(function() {
                        $(this).on('click', function (e) {

                            let selectedBox = $(e.target);
                            let rowMock =
                                '<div class="col-md-4">' +
                                '<p><span>{data.brand}</span><br />' +
                                '<span>{data.code}</span></p>' +
                                '</div>' +
                                '<div class="col-md-5">' +
                                '<div class="form-group form-group-default selectize-enabled required">' +
                                '<label>Taglia</label>' +
                                '<select class="full-width selectpicker" data-product="{data.code}" name="sku" placeholder="Seleziona la taglia"></select>' +
                                '</div>' +
                                '</div>';
                            let that = $(this).children('.product-selection-box');
                            let element = rowMock
                                .replaceAll("{data.brand}", that.find('.product-brand').data('brand'))
                                .replaceAll("{data.code}", that.data('code'))
                                .replaceAll("{data.publicUrl}", that.data('publicUrl'));
                            element = $(element);
                            modal.writeBody(element);

                            Pace.ignore(function () {
                                $.ajax({
                                    url: "/blueseal/xhr/GetProductSkuDetails",
                                    dataType: "json",
                                    data: {
                                        productId: that.data('code')
                                    }
                                }).fail(function(res){
                                    modal.writeBody('OOPS! C\'è stato un problema!');
                                    modal.setOkEvent(function(){
                                        modal.hide();
                                    });
                                    modal.enableOkButton();
                                }).done(function (res) {
                                    modal.enableOkButton();
                                    let asd = element.find('select');
                                    asd.selectize({
                                        valueField: 'skuCode',
                                        labelField: 'label',
                                        searchField: ['label'],
                                        options: res,
                                    });
                                    let selectSku = $('select[name="sku"]');
                                    //let selectizeSku = $(selectSku).selectize()[0].selectize;

                                    modal.setOkEvent(function () {
                                        Pace.ignore(function() {
                                            let stringId = selectSku.val();
                                            let urlDecoded = $.myDecodeGetStringFromUrl(window.location.href);
                                            let orderId = urlDecoded.params.order;
                                            $.ajax(
                                                {
                                                    url: '/blueseal/xhr/OrderLineAddToOrder',
                                                    method: 'POST',
                                                    data: {productSkuStringId: stringId, orderId: orderId}
                                                }
                                            ).fail(function() {
                                                modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore.');
                                                modal.setOkEvent(function(){
                                                    modal.hide();
                                                });
                                            }).done(function() {
                                                modal.writeBody('Riga d\'ordine correttamente inserita');
                                                modal.setOkEvent(function(){
                                                    modal.hide();
                                                    window.location.reload();
                                                });
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            }
        });
    }).fail(function(res){
        modal.writeBody("OOPS! C'è stato un problema nel recupero delle informazioni");
        console.error(res);
        modal.hideCancelBtn();
        modal.setOkEvent(function(){
            modal.hide();
        });
    });
});