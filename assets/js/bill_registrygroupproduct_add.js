$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'BillRegistryCategoryProduct'

    },
    dataType: 'json'
}).done(function (res2) {
    var selectgroupBillRegistryCategoryProductId = $('#groupBillRegistryCategoryProductId');
    if (typeof (selectgroupBillRegistryCategoryProductId[0].selectize) != 'undefined') selectgroupBillRegistryCategoryProductId[0].selectize.destroy();
    selectgroupBillRegistryCategoryProductId.selectize({
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
        table: 'BillRegistryTypeTaxes'

    },
    dataType: 'json'
}).done(function (res2) {
    var selectgroupBillRegistryTypeTaxesId = $('#groupBillRegistryTypeTaxesId');
    if (typeof (selectgroupBillRegistryTypeTaxesId[0].selectize) != 'undefined') selectgroupBillRegistryTypeTaxesId[0].selectize.destroy();
    selectgroupBillRegistryTypeTaxesId.selectize({
        valueField: 'id',
        labelField: 'description',
        searchField: 'description',
        options: res2,
    });

});

$(document).on('bs.GroupProductIwes.save', function () {
    let bsModal = new $.bsModal('Inserimento Gruppo Prodotti', {
        body: `<p>Confermare?</p>`
    });
    bsModal.setOkEvent(function () {
        bsModal.showCancelBtn();
        var isActive;
        if ($('#groupIsActive').prop('checked', true)) {
            isActive = 1;

        } else {
            isActive = 0;
        }
        const data = {

            codeProduct: $('#groupCodeProduct').val(),
            nameProduct: $('#groupNameProduct').val(),
            billRegistryCategoryProductId: $('#groupBillRegistryCategoryProductId').val(),
            billRegistryTypeTaxesId: $('#groupBillRegistryTypeTaxesId').val(),
            um: $('#groupUm').val(),
            description: $('#groupDescription').val(),
            price: $('#groupPrice').val(),
            cost: $('#groupCost').val(),
            productType: $('#groupProductType').val(),
            isActive: isActive

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryGroupProductManageAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});


