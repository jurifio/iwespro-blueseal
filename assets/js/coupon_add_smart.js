const couponTypes = [];
const initTypeSelect = function () {
    var selectContainer = $('#couponTypeId').parent();
    var selectContent = selectContainer.html();
    selectContainer.html('<img src="/assets/img/ajax-loader.gif">');

    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'CouponType'
            },
            dataType: 'json'
        }).done(function (res) {
            selectContainer.html(selectContent);
            var select = $('#couponTypeId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name','amount'],
                options: res,
                render: {
                    item: function (item, escape) {
                        var caption = 'valore: ' + item.amount +(item.amountType === 'F' ? '€' : '%')+',    validità:' + item.validity + ', minimo spesa:' + item.validForCartTotal+(item.hasFreeShipping === 1 ? ', spedizione gratuita' : '')+(item.hasFreeReturn === 1 ? ', reso gratuito' : '');
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>&nbsp;&nbsp;' +
                            '<span class="caption">' + escape(caption) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        var caption = 'valore: ' + item.amount +(item.amountType === 'F' ? '€' : '%')+',    validità:' + item.validity + ', minimo spesa:' + item.validForCartTotal+(item.hasFreeShipping === 1 ? ', spedizione gratuita' : '')+(item.hasFreeReturn === 1 ? ', reso gratuito' : '');
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>&nbsp;&nbsp;' +
                            '<span class="caption">' + escape(caption) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
};

$(document).on('bs.coupontype.refresh', function () {
    initTypeSelect();
});

$(document).ready(function () {
    initTypeSelect();
    var singleTemplateCall = $.getTemplate('singleCouponInputsMock').promise();
    var multiTemplateCall = $.getTemplate('multipleCouponInputsMock').promise();
    const inputsContainerSelector = '.coupon-specifics';

    $.when(singleTemplateCall, multiTemplateCall).then(function (singleTemplate, multiTemplate) {
        "use strict";
        singleTemplate = singleTemplate[0];
        multiTemplate = multiTemplate[0];

        $(document).on('change', '[name="couponSpecies"]', function () {
            console.log($(this));
            console.log($(this).val());
            switch ($(this).val()) {
                case 'single': {
                    $(inputsContainerSelector).html(singleTemplate);
                    handleSingleInputs();
                    break;
                }
                case 'multi': {
                    $(inputsContainerSelector).html(multiTemplate);
                    handleMultiInputs();
                    break;
                }
            }
        });

    });
});

const handleSingleInputs = function () {
    "use strict";
    Pace.ignore(function () {
        let couponTypeSelect = $('#couponTypeId');
        let couponType = couponTypeSelect[0].selectize.options[couponTypeSelect.val()];
        $('#amount').val(couponType.amount);
        console.log(couponType);

        var serialContainer = $('#code').parent();
        var serialContent = serialContainer.html();
        serialContainer.html('<img src="/assets/img/ajax-loader.gif">');
        $.ajax({
            url: '/blueseal/xhr/SerialNumberProvider'
        }).done(function (res) {
            serialContainer.html(serialContent);
            $('#code').val(res);
        });

        var userContainer = $('select[name=\"userId\"]').parent();
        var userContent = userContainer.html();
        userContainer.html('<img src="/assets/img/ajax-loader.gif">');
        $.ajax({
            url: "/blueseal/xhr/UserData",
            dataType: "json"
        }).done(function (res) {
            userContainer.html(userContent);
            var select = $('select[name=\"userId\"]');
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
    });
};

const handleMultiInputs = function () {
    "use strict";

};

$(document).on('bs.couponsmart.add',function () {
    var data = $('form').serializeObject();
    let url = "";
    switch (data.couponSpecies) {
        case 'single':
            url = "/blueseal/coupon/aggiungi";
            break;
        case 'multi':
            url = "/blueseal/eventocoupon/aggiungi";
            break;
    }
    delete data.couponSpecies;

    var bsModal = new $.bsModal('Aggiungi Coupon',
        {
            body: 'Sei sicuro di voler inserire il coupon?'
        }
    );

    bsModal.setOkLabel('Inserisci');

    bsModal.setOkEvent(function () {
        bsModal.hideCancelBtn();
        bsModal.setOkLabel('Fatto');
        bsModal.setOkEvent(function() {
            "use strict";
            bsModal.hide();
        });
        bsModal.hideOkBtn();
        bsModal.showLoader();
        $.ajax({
            type: "POST",
            url: url,
            data: data
        }).done(function (content){
            bsModal.showOkBtn();
            bsModal.setCloseEvent(function () {
                window.location.reload();
            });
            bsModal.writeBody('Salvataggio Riuscito');
        }).fail(function(){
            bsModal.writeBody('Errore nell\'inserimento');
        });
    });


});
