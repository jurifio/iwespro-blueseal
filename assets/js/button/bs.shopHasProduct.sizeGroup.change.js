window.buttonSetup = {
    tag: "a",
    icon: "fa-wrench",
    permission: "/admin/product/edit&&allShops",
    event: "bs-manage-shop-sizeGroups",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Assegna Gruppi taglie",
    placement: "bottom"
};

$(document).on('bs-manage-shop-sizeGroups', function () {

    var selectedRows = $.getDataTableSelectedRowsData();

    if (selectedRows.length < 1) {
        return false;
    }

    let bsModal = new $.bsModal('Cambia gruppo Taglie Privato', {});
    bsModal.getElement().find('.modal-body').css('min-height','350px');
    bsModal.showLoader();
    Pace.ignore(function () {
        $.ajax({
            url: "/blueseal/xhr/ChangePrivateProductSizeGroupController",
            type: "GET",
            data: {
                shopHasProducts: selectedRows
            },
            dataType: 'json'
        }).done(function (response) {
            bsModal.writeBody('<div class="form-group form-group-default selectize-enabled required">' +
                '<label>Gruppo Taglie Privato</label>' +
                '<select class="full-width" id="productSizeGroupId" placeholder="Seleziona il gruppo taglie"></select>' +
                '</div>' +
                '<div class="form-group form-group-default">' +
                '<label>Forza Cambiamento in caso di incompatibilità</label>' +
                '<input class="form-control" type="checkbox" value="true" id="forceChange" >' +
                '</div>');
            let select = bsModal.getElement().find('#productSizeGroupId');
            if (select.length > 0 && typeof select[0].selectize !== 'undefined') select[0].selectize.destroy();
            let productSizeGroupsCopy = [];
            for(let productSizeGroup of response) {
                productSizeGroup.macroName = productSizeGroup.productSizeMacroGroup.name;
                productSizeGroupsCopy.push(productSizeGroup)
            }
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['macroName','locale'],
                options: productSizeGroupsCopy,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.locale+ ' '+ item.macroName) + '</span>' +
                            ' - <span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.locale+ ' '+ item.macroName) + '</span>' +
                            ' - <span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
            bsModal.setCancelLabel("Annulla");
            bsModal.showCancelBtn();
            bsModal.setOkLabel("Assegna");
            bsModal.setOkEvent(function () {
                Pace.ignore(function () {
                    let value = select.val();
                    let forceChange = $('#forceChange:checked').length > 0;
                    if(value.length === 0) return;
                    bsModal.showLoader();
                    bsModal.setOkLabel('Ok');
                    bsModal.hideOkBtn();
                    bsModal.hideCancelBtn();
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        $.refreshDataTable();
                    });
                    $.ajax({
                        url: "/blueseal/xhr/ChangePrivateProductSizeGroupController",
                        type: "PUT",
                        data: {
                            shopHasProducts: selectedRows,
                            productSizeGroupId: value,
                            forceChange: forceChange
                        }
                    }).done(function (response) {
                        bsModal.writeBody('Fatto');
                        bsModal.showOkBtn();
                    }).fail(function (res) {
                        bsModal.writeBody('Errore');
                        bsModal.showOkBtn();
                    });
                });
            });
        }).fail(function (response) {
            bsModal.writeBody(response.responseText);
            bsModal.setOkLabel('Chiudi');
        });
    });
});