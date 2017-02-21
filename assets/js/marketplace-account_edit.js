$(document).on('bs.marketplace-account.save', function () {
    let method;
    let data = {};


    data.billingAddressBook = readShipment('#billingAddress');
    data.shippingAddresses = [];
    $.each($('#shippingAddresses .shippingAddress'), function (k, v) {
        data.shippingAddresses.push(readShipment(v));
    });

    if (data.id.length) {
        method = "PUT";
    } else {
        method = "POST";
    }

    $.ajax({
        method: method,
        url: "/blueseal/xhr/ShopManage",
        data: {
            shop: data
        }
    }).done(function () {
        new Alert({
            type: "success",
            message: "Modifiche Salvate"
        }).open();
    }).fail(function (e) {
        console.log(e);
        new Alert({
            type: "danger",
            message: "Impossibile Salvare"
        }).open();
    });
});

(function ($) {
    let params = $.decodeGetStringFromUrl(window.location.href);
    if (typeof params.id != 'undefined') {
        $.ajax({
            url: "/blueseal/xhr/MarketplaceAccountManage",
            data: {
                id: params.id
            },
            dataType: "json"
        }).done(function (res) {
            $('#marketplace_account_marketplace_id').val(res.id);
            $('#marketplace_account_id').val(res.title);
            $('#shop_owner').val(res.owner);
            $('#shop_referrerEmails').val(res.referrerEmails);
            $('#shop_iban').val(res.iban);
            $('#shop_currentSeasonMultiplier').val(res.currentSeasonMultiplier);
            $('#shop_pastSeasonMultiplier').val(res.pastSeasonMultiplier);
            $('#shop_saleMultiplier').val(res.saleMultiplier);
            $('#shop_config_refusalRate').val(res.config.refusalRate);
            $('#shop_config_refusalRate_lastMonth').val(res.config.refusalRateLastMonth);
            $('#shop_config_reactionRate').val(res.config.reactionRate);
            $('#shop_config_reactionRate_lastMonth').val(res.config.reactionRateLastMonth);
            $('#shop_config_accountStatus').val(res.config.accountStatus);
            $('#shop_config_accountType').val(res.config.accountType);
            $('#shop_config_photoCost').val(res.config.photoCost);
            $('#shop_config_shootingTransportCost').val(res.config.shootingTransportCost);
            $('#shop_config_orderTransportCost').val(res.config.orderTransportCost);

            checkPermission('allShops')
                .done(function () {
                    $.each($('input[disabled]'), function (k, v) {
                        $(v).prop("disabled", false)
                            .closest('div')
                            .removeClass('disabled')
                            .prop("disabled", false);
                    });
                }).fail(function () {
                "use strict";

            });

            $('#shop_referrerEmails').selectize({
                delimiter: ';',
                create: function (input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
            appendShipment(res.billingAddressBook, '#billingAddress');
            res.shippingAddressBook.forEach(function (addressData) {
                appendShipment(addressData, '#shippingAddresses');
            });
            appendShipment({}, '#shippingAddresses');
        });
    }
})(jQuery);

function readShipment(containerSelector) {
    "use strict";
    let data = {};
    let element = $(containerSelector);
    data.id = element.find('#id').val();
    data.name = element.find('#name').val();
    data.subject = element.find('#subject').val();
    data.address = element.find('#address').val();
    data.extra = element.find('#extra').val();
    data.city = element.find('#city').val();
    data.countryId = element.find('#country').val();
    data.postcode = element.find('#postcode').val();
    data.phone = element.find('#phone').val();
    data.cellphone = element.find('#cellphone').val();
    data.province = element.find('#province').val();
    return data;
}

function appendShipment(data, containerSelector) {
    let container = $(containerSelector);
    $.getTemplate('addressBookFormMock').done(function (res) {
        let element = $(res);
        Pace.ignore(function () {
            $.get({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            }).done(function (res2) {
                let select = element.find('#country');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res2,
                });
                if (data != null && Object.keys(data).length > 0) {
                    element.find('#id').val(data.id);
                    element.find('#name').val(data.name);
                    element.find('#subject').val(data.subject);
                    element.find('#address').val(data.address);
                    element.find('#extra').val(data.extra);
                    element.find('#city').val(data.city);
                    select[0].selectize.setValue(data.countryId);
                    element.find('#postcode').val(data.postcode);
                    element.find('#phone').val(data.phone);
                    element.find('#cellphone').val(data.cellphone);
                    element.find('#province').val(data.province);
                }
                container.append(element);
            });
        });
    });
}

