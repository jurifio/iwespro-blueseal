$(document).ready(function () {
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Carrier'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#carrierId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'AddressBook'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#fromAddressBookId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'subject',
            searchField: ['subject'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.subject) + '</span> - ' +
                        '<span class="caption">address:' + escape(item.address + 'city:' + item.city) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.subject) + '</span> - ' +
                        '<span class="caption">address:' + escape(item.address + 'city:' + item.city) + '</span>' +
                        '</div>'
                }
            }
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/SelectOrderLineAjaxController',
        data: {
            id: 1,

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#order');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'id',
            searchField: ['id'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">'+escape(item.shopName)+ '-' + escape(item.id)  + '</span> - ' +
                        '<span class="caption"> product: ' + escape(item.productId+' - '+item.productVariantId +item.productSizeId+ ' prezzo: ' + item.netPrice) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">'+escape(item.shopName)+'-' + escape(item.id)  + '</span> - ' +
                        '<span class="caption"> product: ' + escape(item.productId+' - '+item.productVariantId +item.productSizeId+ ' prezzo: ' + item.netPrice) + '</span>' +
                        '</div>'
                }
            }
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'AddressBook'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#toAddressBookId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'subject',
            searchField: ['subject'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.subject) + '</span> - ' +
                        '<span class="caption">address:' + escape(item.address + 'city:' + item.city) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.subject) + '</span> - ' +
                        '<span class="caption">address:' + escape(item.address + 'city:' + item.city) + '</span>' +
                        '</div>'
                }
            }
        });

    });



    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#fromCountryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#toCountryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });


    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2
        });

    });



});






$(document).on('bs.shipment.save', function () {
    let bsModal = new $.bsModal('Inserimento Spedizione', {
        body: '<p>Confermare?</p>'
    });

    var config = '?carrierId=' + $("#carrierId").val() + '&' +
        'deliveryDate=' + $("#deliveryDate").val() + '&' +
        'bookingNumber=' + $("#bookingNumber").val() + '&' +
        'trackingNumber=' + $("#trackingNumber").val() + '&' +
        'shopId=' + $("#shopId").val() + '&' +
        'realShipmentPrice=' + $("#realShipmentPrice").val() + '&' +
        'shipmentInvoiceNumber=' + $("#shipmentInvoiceNumber").val() + '&' +
        'dateInvoice=' + $("#dateInvoice").val() + '&' +
        'isOrder=' + $("#isOrder").val() + '&' +
        'order='+ $("#order").val() + '&' +
        'scope='+ $("#scope").val() + '&' +
        'fromAddressBookId=' + $("#fromAddressBookId").val() + '&' +
        'fromName=' + $("#fromName").val() + '&' +
        'fromAddress=' + $("#fromAddress").val() + '&' +
        'fromSubject=' + $("#fromSubject").val() + '&' +
        'fromExtra=' + $("#fromExtra").val() + '&' +
        'fromCity=' + $("#fromCity").val() + '&' +
        'fromCountryId=' + $("#fromCountryId").val() + '&' +
        'fromPostCode=' + $("#fromPostCode").val() + '&' +
        'fromPhone=' + $("#fromPhone").val() + '&' +
        'fromCellphone=' + $("#fromCellphone").val() + '&' +
        'fromVatNumber=' + $("#fromVatNumber").val() + '&' +
        'fromProvince=' + $("#fromProvince").val() + '&' +
        'fromIban=' + $("#fromIban").val() + '&' +
        'toAddressBookId=' + $("#toAddressBookId").val() + '&' +
        'toName=' + $("#toName").val() + '&' +
        'toSubject=' + $("#toSubject").val() + '&' +
        'toAddress=' + $("#toAddress").val() + '&' +
        'toExtra=' + $("#toExtra").val() + '&' +
        'toCity=' + $("#toCity").val() + '&' +
        'toCountryId=' + $("#toCountryId").val() + '&' +
        'toPostCode=' + $("#toPostCode").val() + '&' +
        'toPhone=' + $("#toPhone").val() + '&' +
        'toCellphone=' + $("#toCellphone").val() + '&' +
        'toVatNumber=' + $("#toVatNumber").val() + '&' +
        'toProvince=' + $("#toProvince").val() + '&' +
        'toIban=' + $("#toIban").val();


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/ShipmentManageAjaxController" + config;
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
            setTimeout()
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                setTimeout(function(){
                    window.location.href = '/blueseal/spedizioni/aggiungi';
                }, 1000);

            });
            bsModal.showOkBtn();


        });
    });
});

$('#fromAddressBookId').change(function () {
    var selectionFromAddressBookId = $('#fromAddressBookId').val();
    document.getElementById('fromName').value = '';
    document.getElementById('fromSubject').value = '';
    document.getElementById('fromAddress').value = '';
    document.getElementById('fromExtra').value = '';
    document.getElementById('fromCity').value = '';
    var selectizefromCountryId = $("#fromCountryId")[0].selectize;
    selectizefromCountryId.clear();
    document.getElementById('fromPostCode').value = '';
    document.getElementById('fromCellphone').value = '';
    document.getElementById('fromVatNumber').value = '';
    document.getElementById('fromProvince').value = '';
    document.getElementById('fromIban').value = '';



        $.ajax({
            url: '/blueseal/xhr/SelectAddressBookAjaxController',
            method: 'get',
            data: {
                id: selectionFromAddressBookId
            },
            dataType: 'json'
        }).done(function (res) {

            $.each(res, function (k, v) {
                document.getElementById('fromName').value = v.name;
                document.getElementById('fromSubject').value = v.subject;
                document.getElementById('fromAddress').value = v.address;
                document.getElementById('fromExtra').value = v.extra;
                document.getElementById('fromCity').value = v.city;
                //document.getElementById('fromCountryId').value = v.countryId;
                $('#fromCountryId').data('selectize').setValue(v.countryId);
                document.getElementById('fromPostCode').value = v.postcode;
                document.getElementById('fromCellphone').value = v.cellphone;
                document.getElementById('fromVatNumber').value = v.vatNumber;
                document.getElementById('fromProvince').value = v.province;
                document.getElementById('fromIban').value = v.iban;
            });
        });
});


$('#toAddressBookId').change(function () {
    var selectionToAddressBookId = $('#toAddressBookId').val();
    document.getElementById('toName').value = '';
    document.getElementById('toSubject').value = '';
    document.getElementById('toAddress').value = '';
    document.getElementById('toExtra').value = '' ;
    document.getElementById('toCity').value = '';
    var selectizetoCountryId = $("#toCountryId")[0].selectize;
    selectizetoCountryId.clear();
    document.getElementById('toPostCode').value = '';
    document.getElementById('toCellphone').value = '';
    document.getElementById('toVatNumber').value = '';
    document.getElementById('toProvince').value = '';
    document.getElementById('toIban').value = '';



    $.ajax({
        url: '/blueseal/xhr/SelectAddressBookAjaxController',
        method: 'get',
        data: {
            id: selectionToAddressBookId
        },
        dataType: 'json'
    }).done(function (res) {

        $.each(res, function (k, v) {
            document.getElementById('toName').value = v.name;
            document.getElementById('toSubject').value = v.subject;
            document.getElementById('toAddress').value = v.address;
            document.getElementById('toExtra').value = v.extra;
            document.getElementById('toCity').value = v.city;
            $('#toCountryId').data('selectize').setValue(v.countryId);
            document.getElementById('toPostCode').value = v.postcode;
            document.getElementById('toCellphone').value = v.cellphone;
            document.getElementById('toVatNumber').value = v.vatNumber;
            document.getElementById('toProvince').value = v.province;
            document.getElementById('toIban').value = v.iban;
        });
    });
});