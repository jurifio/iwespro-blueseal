$(document).on('bs.prices.edit', function () {
    $('.form-prices-per-product').bsForm(
        'save',
        {
            url: '/blueseal/xhr/ProductPriceEdit',
            method: 'post',
            dataType: 'JSON',
            excludeEmptyFields: false,
            onDone: function (res) {
                if (true == res) {
                    modal = new $.bsModal(
                        'Modifica prezzi',
                        {body: 'Prezzi aggiornati!'}
                    );
                } else {
                    modal = new $.bsModal(
                        'Modifica prezzi',
                        {body: 'OOPS! ' + res}
                    )
                }
            },
            onFail: function (res) {
                modal = new $.bsModal(
                    'Modifica prezzi',
                    {body: res}
                )
            }
        }
    );
});

$(document).ready(function () {

    var form = $('.form-prices-per-product');
    var addRowBtn = $('.addRow');
    var rowCount = 0;
    var rowPriceTemplate = $('.row-price');
    var rowPrice = rowPriceTemplate.clone();
    rowPriceTemplate.remove();

    var code = $_GET.get('code');
    if (false === code) {
        modal = new $.bsModal(
            'Errore!',
            {body: 'Non ho niente da fare. Devi selezionare un prodotto dalla lista dei prodotti'}
        );
        $('.form-container').html('');
    } else {
        $.ajax({
            url: '/blueseal/xhr/ProductPriceEdit',
            type: 'get',
            dataType: 'json',
            data: {code: code}
        }).done(function (res) {
            if (false === res) {
                modal = new $.bsModal(
                    'Errore!',
                    {body: 'Il prodotto sembra non esistere'}
                );
            } else {
                $('h3').html(code);
                var ids = code.split('-');
                $('input[name="id"]').val(ids[0]);
                $('input[name="productVariantId"]').val(ids[1]);
                var len = res.length;
                if (0 === len) {
                    addRow();
                }
                for (var i in res) {
                    addRow(i, res[i]);
                }
            }
        });
    }

    var addRow = function (shopId, data) {
        var row = rowPrice.clone();
        row.addClass('shp-' + rowCount);
        rowCount++;

        var shopSelect = row.find('select');
        shopSelect.addClass('shop-' + rowCount);
        shopSelect.attr('name', 'shopId-' + rowCount);
        var extId = row.find('.extId');
        extId.addClass('extId-' + rowCount);
        extId.attr('name', 'extId-' + rowCount);

        var value = row.find('.value');
        value.addClass('value-' + rowCount);
        value.attr('name', 'value-' + rowCount);
        var price = row.find('.price');
        price.addClass('price-' + rowCount);
        price.attr('name', 'price-' + rowCount);
        var salePrice = row.find('.salePrice');
        salePrice.addClass('salePrice-' + rowCount);
        salePrice.attr('name', 'salePrice-' + rowCount);
        form.find('.panel-body').append(row);

        var removeBtn = row.find('.removeRow');
        removeBtn.on('click', function (e) {

            var code = $('.price-manage-heading h3').html().split('-');
            var id = code[0];
            var productVariantId = code[1];
            var thisRow = $(this).closest('.row-price');
            var shopId = thisRow.find('.select-shop').val();

            e.preventDefault();
            if ('undefined' !== typeof data) {
                $.ajax({
                    url: '/blueseal/xhr/ProductPriceEdit',
                    method: 'delete',
                    data: {id: id, productVariantId: productVariantId, shopId: shopId}
                }).done(function (res) {
                    if ('ok' == res) var bodyMsg = 'Lo shop è stato eliminato';
                    else if ('ko' == res) var bodyMsg = 'Ci sono Sku associati a questo shop. Non posso cancellarlo';
                    model = new $.bsModal(
                        'Elimino uno shop dal prodotto',
                        {
                            body: bodyMsg,
                            okButtonEvent: function() {
                                model.hide();
                                if ('ok' == res) {
                                    if (1 < $('.row-price').length) {
                                        row.remove();
                                    } else {
                                        row.find('select').val('');
                                        row.find('.extId').val('');
                                        row.find('.value').val('');
                                        row.find('.price').val('');
                                        row.find('.salePrice').val('');
                                    }
                                }
                            }
                        }
                    );
                }).fail(function () {
                    model = new $.bsModal(
                        'Elimino uno shop dal prodotto',
                        {
                            body: 'OOPS! Errore di sistema. Contatta un amministratore',
                            okButtonEvent: function() {
                                model.hide();
                            }
                        }
                    );
                });

            } else {
                if (1 < $('.row-price').length) {
                    row.remove();
                } else {
                    row.find('select').val('');
                    row.find('.extId').val('');
                    row.find('.value').val('');
                    row.find('.price').val('');
                    row.find('.salePrice').val('');
                }
            }
        });

        if ('undefined' !== typeof data) {
            shopSelect.val(shopId);
            if (0 == data['shopChange']) {
                shopSelect.prop('disabled', 'disabled');
                removeBtn.prop('disabled', 'disabled');
            }
            extId.val(data['extId']);
            value.val(data['value']);
            price.val(data['price']);
            salePrice.val(data['salePrice']);
        }
    };

    addRowBtn.on('click', function (e) {
        e.preventDefault();
        addRow();
    });
});