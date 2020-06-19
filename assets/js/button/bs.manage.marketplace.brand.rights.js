window.buttonSetup = {
    tag: "a",
    icon: "fa-handshake-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-manage-marketplace-brand-rights",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Applica la regola di Pubblicazione per Marketplace  ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-manage-marketplace-brand-rights', function (e, element, button) {

    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Brand  per poterli Associare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);

    });
    var bsModal = new $.bsModal('Seleziona il Marketplace', {
        body: '<label for="marketplaceShopId">Seleziona il Marketplace</label><br />' +
            '<select id="marketplaceShopId" name="marketplaceShopId" class="full-width selectize"></select><br />'
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'MarketplaceHasShop'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#marketplaceShopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2
        });

    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data={
            marketplaceShopId:  $('#marketplaceShopId').val(),
            brands:getVarsArray
        };
        $.ajax({
            method: 'POST',
            url: "/blueseal/xhr/ManageMarketplaceHasShopBrandAjaxController",
            data:data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });
});