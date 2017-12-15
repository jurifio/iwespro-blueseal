window.buttonSetup = {
    tag: "a",
    icon: "fa-magic",
    permission: "/admin/product/edit&&allShops",
    event: "bs-manage-sizeGroups",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Assegna Gruppi Taglia Pubblici",
    placement: "bottom"
};

$(document).on('bs-manage-sizeGroups', function () {

    var selectedRows = $.getDataTableSelectedRowsData();

    if (selectedRows.length < 1) {
        return false;
    }

    let bsModal = new $.bsModal('Cambia gruppo Taglie Pubblico', {});
    bsModal.showLoader();
    Pace.ignore(function () {
        $.ajax({
            url: "/blueseal/xhr/ChangePublicProductSizeGroupController",
            type: "GET",
            data: {
                products: selectedRows
            },
            dataType: 'json'
        }).done(function (response) {
            bsModal.writeBody('<div class="form-group form-group-default selectize-enabled required">' +
                '<label>Gruppo Taglie Pubblico</label>' +
                '<select class="full-width" id="productSizeGroupId" placeholder="Seleziona il gruppo taglie"></select>' +
                '</div>');
            let select = bsModal.getElement().find('#productSizeGroupId');
            if (select.length > 0 && typeof select[0].selectize !== 'undefined') select[0].selectize.destroy();
            let productSizeGroupsCopy = [];
            for(let productSizeGroup of response) {
                productSizeGroup.macroName = productSizeGroup.productSizeMacroGroup.name;
                productSizeGroup.sizeNames = [];
                for(let productSize of productSizeGroup.productSize) {
                    if( ($.inArray(productSize.name, productSizeGroup.sizeNames)) === -1 ){
                        productSizeGroup.sizeNames.push(productSize.name);
                    }
                }
                productSizeGroup.sizeNames = productSizeGroup.sizeNames.join('|');
                productSizeGroupsCopy.push(productSizeGroup);
            }
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['macroName','locale','sizeNames'],
                options: productSizeGroupsCopy,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.locale+ ' '+ item.macroName) + '</span>' +
                            ' - <span class="caption">' + escape(item.sizeNames) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.locale+ ' '+ item.macroName) + '</span>' +
                            ' - <span class="caption">' + escape(item.sizeNames) + '</span>' +
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
                        url: "/blueseal/xhr/ChangePublicProductSizeGroupController",
                        type: "PUT",
                        data: {
                            products: selectedRows,
                            productSizeGroupId: value
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