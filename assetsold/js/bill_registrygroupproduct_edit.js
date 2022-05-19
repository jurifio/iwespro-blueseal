
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
            billRegistryGroupProductId:$('#billRegistryGroupProductId').val(),
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
            method: 'put',
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


