(function ($) {
    Pace.ignore(function () {
        var container = $('select[name=\"user\"]').parent();
        var content = container.html();
        container.html('<img src="/assets/img/ajax-loader.gif">');
        $.ajax({
            url: "/blueseal/xhr/UserList",
            dataType: "json"
        }).done(function (res) {
            container.html(content);
            select = $('select[name=\"user\"]');
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['email', 'name', 'surname'],
                options: res,
                render: {
                    item: function (item, escape) {
                        var label = item.name + ' ' + item.surname || item.email;
                        var caption = (item.name + item.surname).length > 0 ? item.email : null;
                        return '<div>' +
                            '<span class="label">' + escape(label) + '</span>' +
                            (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                            '</div>'
                    },
                    option: function (item, escape) {
                        var label = item.name + ' ' + item.surname || item.email;
                        var caption = (item.name + item.surname).length > 0 ? item.email : null;
                        return '<div>' +
                            '<span class="label">' + escape(label) + '</span>' +
                            (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'OrderPaymentMethod'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#orderPaymentMethod');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
    });
})(jQuery);

$(document).on('keypress', '#product-search', function (e) {
    "use strict";
    if (13 == e.charCode) {
        e.preventDefault();
        $(this).select();
        var query = $('#product-search').val();
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
            var bsModal = $('#bsModal');
            var header = $('#bsModal .modal-header h4');
            var body = $('#bsModal .modal-body');
            var cancelButton = $('#bsModal .modal-footer .btn-default');
            var okButton = $('#bsModal .modal-footer .btn-success');

            var itemMock = '<div class="col-xs-4">' +
                '<div id="product_code_{data.code}" data-code="{data.code}" data-public-url="{data.publicUrl}" class="product-selection-box">' +
                '<img class="img-responsive thumbnail" src="{data.dummyUrl}" />' +
                '<p class="product-brand" data-brand="{data.brand}"><span class="small">Brand: </span>{data.brand}</p>' +
                '<p><span class="small">Code: </span>{data.code}</p>' +
                '<p><span class="small">Cpf: </span>{data.cpf}</p>' +
                '<p><span class="small">Taglie Disp.: </span>{data.sizes}</p>' +
                '</div>' +
                '</div>';

            var hbody = '<div class="row">';
            var obj;
            for (var i in res) {
                obj = res[i];
                hbody += itemMock
                    .replaceAll('{data.dummyUrl}', obj.dummyUrl)
                    .replaceAll('{data.brand}', obj.brand)
                    .replaceAll('{data.code}', obj.code)
                    .replaceAll('{data.publicUrl}', obj.publicUrl)
                    .replaceAll('{data.cpf}', obj.cpfVar)
                    .replaceAll('{data.sizes}', obj.availableSizes.join(' - '));
            }
            hbody += "</div>";
            header.html('Scegli Prodotto');
            body.html(hbody);
            bsModal.modal();
            cancelButton.on('click', function () {
                bsModal.hide();
            });
            $('.product-selection-box').on('click', function () {
                if ($(this).hasClass('product-box-selected')) {
                    $(this).removeClass('product-box-selected');
                } else {
                    $(this).addClass('product-box-selected');
                }
            });
            var rowMock = '<div class="row row-borded order-line">' +
                '<div class="col-md-4">' +
                '<p><span>{data.brand}</span>' +
                '<span>{data.code}</span></p>' +
                '</div>' +
                '<div class="col-md-5">' +
                '<div class="form-group form-group-default selectize-enabled required">' +
                '<label>Taglia</label>' +
                '<select class="full-width selectpicker" data-product="{data.code}" name="sku" placeholder="Seleziona la taglia"></select>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-3">' +
                '<a href="#" class="redColor" name="product-row-delete" data-code="{data.code}"><i class="fa fa-times-circle fa-2x" aria-hidden="true"></i></a>' +
                '<a href="{data.publicUrl}" target="_blank"><i class="fa fa-external-link-square fa-2x" aria-hidden="true"></i></a>' +
                '</div>' +
                '</div>';
            var lineContainer = $('#orderLineContainer');
            okButton.off().on('click', function () {
                $('.product-selection-box.product-box-selected').each(function () {
                    var that = $(this);
                    var element = rowMock
                        .replaceAll("{data.brand}", that.find('.product-brand').data('brand'))
                        .replaceAll("{data.code}", that.data('code'))
                        .replaceAll("{data.publicUrl}", that.data('publicUrl'));
                    element = $(element);
                    lineContainer.append(element);
                    Pace.ignore(function() {
                        $.ajax({
                            url: "/blueseal/xhr/GetProductSkuDetails",
                            dataType: "json",
                            data: {
                                productId: that.data('code')
                            }
                        }).done(function(res) {
                            var asd = element.find('select');
                            asd.selectize({
                                valueField: 'skuCode',
                                labelField: 'label',
                                searchField: ['label'],
                                options: res,
                            });
                        });
                    });
                });
            });
        });
    }
});

$(document).on('click','a[name="product-row-delete"]',function () {
    $(this).closest('div.order-line').remove();
});

$(document).on('change', '#user', function () {
    $(document).trigger('bs.address.refresh');
});

$(document).on('click', '#formAddressSubmit', function (event) {
    event.preventDefault();
    var user = $('#user').val();
    if ('undefined' != user && user > 0) {
        var data2 = {};
        data2['user_id'] = user;
        $('#newAddressForm input[name], #newAddressForm select[name]').each(function (k, v) {
            v = $(v);
            if (v.prop('id') == 'undefined' || v.prop('id') == '') return;
            if (v.attr('required') && v.attr('required') == 'required' && v.val() == '') {
                new Alert({
                    type: "warning",
                    message: "C'è un campo obbligatorio da riempire!"
                }).open();
                throw DOMException;
            }
            data2[v.prop('id')] = v.val();
        });

        $.ajax({
            method: "POST",
            url: "/blueseal/xhr/UserAddressManage",
            data: data2
        }).done(function (res) {
            $(document).trigger('bs.address.refresh');
        });
    } else {
        new Alert({
            type: "warning",
            message: "Devi selezionare prima un utente per poter inserire l'indirizzo"
        }).open();
        return false;
    }
});

$(document).on('bs.address.refresh', function () {
    var user = $('#user').val();
    if ('undefined' != user && user > 0) {
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/UserAddressManage",
                data: {
                    userId: user
                },
                dataType: "json"
            }).done(function (res) {
                $("select[name$='Address']").each(function () {
                    var select = $(this);
                    select[0].selectize.destroy();
                    $(select).selectize({
                        valueField: 'id',
                        labelField: 'label',
                        searchField: ['label','city'],
                        options: res,
                        render: {
                            item: function (item, escape) {
                                var label = item.name + ' ' + item.surname;
                                var caption = item.address + ' ' + item.city;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                                    '</div>'
                            },
                            option: function (item, escape) {
                                var label = item.name + ' ' + item.surname;
                                var caption = item.address + ' ' + item.city;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                                    '</div>'
                            }
                        }
                    });
                });
                $(document).trigger('bs.address-form.reload');
            });
        });
    }
});

$(document).on('bs.address-form.reload',function() {
    "use strict";
    $.getTemplate('userAddressFormTemplate').done(function (res) {
        $('#formAddressContainer').html($(res));
        Pace.ignore(function() {
            $.get({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('#user_address_country');
                if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res2,
                });
            });
        });
    })
});

$(document).on('bs.newOrder.save',function() {
    "use strict";
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');
    header.html('Salva Ordine');
    body.html('Sei sicuro di voler inserire l\'ordine?');
    okButton.show();

    okButton.off().on('click', function() {
        cancelButton.hide();
        okButton.hide();
        body.html('<img src="/assets/img/ajax-loader.gif">');
        var data = {};
        data.user = $('#user').val();
        data.coupon = $('#coupon').val();
        data.note = $('#note').val();
        data.orderPaymentMethod = $('#orderPaymentMethod').val();
        data.shippingAddress = $('#shippingAddress').val();
        data.billingAddress = $('#billingAddress').val();
        data.mail = $('#mail').is(':checked') ? true : false;
        data.orderLine = [];
        $('[name="sku"]').each(function() {
            data.orderLine.push($(this).val());
        });
        $.ajax({
            method:"POST",
            url: '#',
            data:data
        }).done(function(res){
            res = JSON.parse(res);
            body.html('<h4>Url di pagamento: </h4><span>'+res.url+'</span><br /><h5>'+' numero ordine: </h5><span>'+res.id+'</span>');
            okButton.off().on('click',function() {
                window.location.href = '/blueseal/ordini';
            });
            okButton.html('Ok');
            okButton.show();
        }).fail(function(res) {
            body.html(res);
            alert('errore');
        });
    });
    bsModal.modal();
});