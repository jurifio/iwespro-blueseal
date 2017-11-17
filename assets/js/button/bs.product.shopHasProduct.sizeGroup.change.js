window.buttonSetup = {
    tag: "a",
    icon: "fa-wrench",
    permission: "/admin/product/edit&&allShops",
    event: "bs-manage-shop-sizeGroups",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Assegna Gruppi Taglie Privati",
    placement: "bottom"
};

$(document).on('bs-manage-shop-sizeGroups', function () {

    let selectedRows = $.getDataTableSelectedRowsData(undefined, false);

    if (selectedRows.length < 1) {
        return false;
    }

    let single = [];
    let multi = [];
    for (let selectedRow of selectedRows) {
        if (selectedRow['shops'] > 1) {
            multi.push(selectedRow['DT_RowId']);
        } else {
            single.push(selectedRow['DT_RowId']);
        }
    }

    if (multi.length === 0 && single.length > 0) {
        modificaSingoli(single);
    } else if (multi.length > 1 && single.length > 0) {
        let bsModal = new $.bsModal('Cambia gruppo Taglie Privato', {
            body: 'Ci sono prodotti con più di uno shop, vuoi continuare ignorandoli?'
        });
        bsModal.writeBody();
        bsModal.setOkEvent(function () {
            modificaSingoli(single);
        });
    } else if (multi.length === 1 && single.length === 0) {
        modificaMultiplo(multi[0]);
    } else {
        new $.bsModal('Cambia gruppo Taglie Privato', {
            body: 'La selezione effettuata non è gestita, riprova selezionando prodotti diversi'
        });
    }
});

const modificaMultiplo = function (selectedRow) {
    "use strict";
    let bsModal = new $.bsModal('Cambia gruppo Taglie Privato', {});

    let ids = selectedRow.split('-');
    let shopHasProductCall = $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: "ShopHasProduct",
            condition: {
                productId: ids[0],
                productVariantId: ids[1]
            },
            fields: [
                "productId",
                "productVariantId",
                "shopId",
                "shop",
                "productSizeGroup"
            ]
        },
        dataType: "json"
    }).then(function(res) {
        if(typeof res === "object" || res instanceof Array) return res;
        return JSON.parse(res);
    });
    let productSizeGroupCall = $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: "ProductSizeGroup",
            fields: [
                "id",
                "name",
                "locale",
                "productSizeMacroGroup"
            ]
        },
        dataType: "json"
    }).then(function(res) {
        if(typeof res === "object" || res instanceof Array) return res;
        return JSON.parse(res);
    });
    Pace.ignore(function () {
        $.when(shopHasProductCall, productSizeGroupCall).then(function (shopHasProducts, productSizeGroups) {
            let html = '';
            for (let shopHasProduct of shopHasProducts) {
                let shopHasProductId = [shopHasProduct.productId,shopHasProduct.productVariantId,shopHasProduct.shopId].join('-');
                html += '<div class="row">' +
                    '<div class="col-sm-12">' +
                        '<div class="form-group form-group-default selectize-enabled required">' +
                            '<label>Gruppo Taglie Privato '+shopHasProduct.shop.name+'</label>' +
                            '<select class="full-width productSizeGroupSelect" ' +
                                'data-id="'+ shopHasProductId +'" ' +
                                'data-preset="'+ shopHasProduct.productSizeGroup.id +'" ' +
                                'placeholder="Seleziona il gruppo taglie"></select>' +
                        '</div>' +
                    '</div>' +
                    '</div>';
            }
            bsModal.writeBody(html);
            bsModal.getElement().find('select.productSizeGroupSelect').each(function () {
                let select = $(this);
                if (select.length > 0 && typeof select[0].selectize !== 'undefined') select[0].selectize.destroy();
                let productSizeGroupsCopy = [];
                for(let productSizeGroup of productSizeGroups) {
                    productSizeGroup.macroName = productSizeGroup.productSizeMacroGroup.name;
                    productSizeGroupsCopy.push(productSizeGroup)
                }
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['macroName','locale'],
                    items: [$(this).data('preset')],
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
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                let dataSet = {};
                bsModal.getElement().find('select.productSizeGroupSelect').each(function () {
                    dataSet[$(this).data('id')] = $(this).val();
                });
                bsModal.hideOkBtn();
                bsModal.hideCancelBtn();
                bsModal.showLoader();
                Pace.ignore(function () {
                    $.ajax({
                        url: '/blueseal/xhr/ChangePrivateProductSizeGroupController',
                        data: {
                            shopHasProductsGroup: dataSet
                        },
                        method: 'POST',
                        dataType: 'json'
                    }).done(function (res) {
                        bsModal.writeBody('Fatto');
                    }).fail(function (res) {
                        bsModal.writeBody('Errore: <br />'+res.responseJSON.message);
                    }).always(function () {
                        bsModal.setOkEvent(function () {
                            $.refreshDataTable();
                            bsModal.hide();
                        });
                        bsModal.showOkBtn();
                    });
                });
            });
        });
    });
};

const modificaSingoli = function (selectedRows) {
    "use strict";

    let bsModal = new $.bsModal('Cambia gruppo Taglie Privato', {});
    bsModal.getElement().find('.modal-body').css('min-height', '350px');
    bsModal.showLoader();

    Pace.ignore(function () {
        $.ajax({
            url: "/blueseal/xhr/ChangePrivateProductSizeGroupController",
            type: "GET",
            data: {
                products: selectedRows
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
                items: [$(this).data('preset')],
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
                    if (value.length === 0) return;
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
                            products: selectedRows,
                            productSizeGroupId: value,
                            forceChange: forceChange
                        }
                    }).done(function (res) {
                        bsModal.writeBody('Fatto');
                    }).fail(function (res) {
                        bsModal.writeBody('Errore: <br />'+res.responseJSON.message);
                    }).always(function () {
                        bsModal.setOkEvent(function () {
                            $.refreshDataTable();
                            bsModal.hide();
                        });
                        bsModal.showOkBtn();
                    });
                });
            });
        }).fail(function (response) {
            bsModal.writeBody(response.responseText);
            bsModal.setOkLabel('Chiudi');
        });
    });
};