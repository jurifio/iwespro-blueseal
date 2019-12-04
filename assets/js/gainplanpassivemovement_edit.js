
$(document).ready(function () {
    //   let data = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
    let invoice = $('select[name=\"gainPlanId\"]');
    let shop = $('select[name=\"shopId\"]');

    $.ajax({
        url: '/blueseal/xhr/GainPlanSelect',
        method: 'get',
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        invoice.selectize({
            valueField: 'id',
            labelField: ['id', 'invoices', 'customerName'],
            searchField: ['id', 'invoices', 'customerName'],
            options: res,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id) + '</span> - ' +
                        '<span class="caption">' + escape(item.invoices + ' ' + item.customerName) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id) + '</span>  - ' +
                        '<span class="caption">' + escape(item.invoices + ' ' + item.customerName) + '</span>' +
                        '</div>'
                }
            }
        });
    });
    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Shop'
            },
            dataType: 'json'
        }).done(function (res2) {
            var selectShop = $('#shopId');
            if (typeof (selectShop[0].selectize) != 'undefined') selectShop[0].selectize.destroy();
            selectShop.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
    });
});

$(document).on('bs.gainplan.passivemovement.save', function () {
    let bsModal = new $.bsModal('Salva il documento', {
        body: '<div><p>Premere ok per Salvare documento'+
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let checked=0;
        if($('#isActive').is(':checked')){
            checked=1;
        }else{
            checked=0;
        }
        const data = {
            idMovement:$('#idMovement').val(),
            invoice : $('#invoice').val(),
            dateMovement: $('#dateMovement').val(),
            amount:$('#amount').val(),
            gainPlanId:$('#gainPlanId').val(),
            checked:checked,
            fornitureName:$('#fornitureName').val(),
            serviceName:$('#serviceName').val(),
            shop:$('#shopId').val(),
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/GainPlanPassiveMovementManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });
});
