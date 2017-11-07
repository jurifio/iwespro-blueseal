window.buttonSetup = {
    tag: "a",
    icon: "fa-compress",
    permission: "/admin/product/edit&&allShops",
    event: "bs-product-merge",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Fondi i prodotti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-merge', function () {

    let selectedRows = $.getDataTableSelectedRowsData('.table', false, 2);
    if (selectedRows.length < 2) return false;
    let rows = [];
    $.each(selectedRows, function (k, v) {
        let row = {};
        let idsVars = v.DT_RowId.split('-');
        row.id = idsVars[0];
        row.productVariantId = idsVars[1];
        row.name = v.brand;
        row.cpf = v.CPF;
        row.brand = v.brand;
        row.shops = v.shops;
        rows.push(row);
    });
    let bsModal = new $.bsModal('Fondi ' + (rows.length) + ' prodotti');
    bsModal.hideCancelBtn();
    bsModal.hideOkBtn();
    bsModal.setCancelLabel('Annulla');
    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ProductMerge',
            type: 'GET',
            data: {rows: rows}
        }).done(function (res) {
            res = JSON.parse(res);
            var error = '';

            //controllo se entrambi i prodotti hanno ordini
            var countOrderedProducts = 0;

            if (false === res.sizeGroupCompatibility) {
                error += "i due prodotti sono associati con gruppi taglia incompatibili."
            }

            if ('' !== error) {
                bsModal.writeBody(':-( Non posso procedere alla fusione:<br />' + error);
                bsModal.hideCancelBtn();
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
            } else {
                var bodyMsg = '<p>Seleziona il prodotto che rimarrà in catalogo:</p><form>';
                var radio = '';
                var selected = false;
                $.each(res.rows, function (k, v) {
                    radio += '<input type="radio" name="choosen" value="' + k + '" ';
                    if ((0 < countOrderedProducts) && (0 == v['areOrders'])) {
                        radio += 'disabled="disabled"'
                    } else {
                        if (false === selected) {
                            radio += 'checked';
                            selected = true;
                        }
                    }
                    radio += ' /> ' + v['id'] + '-' + v['productVariantId'] + ' - CPF: ' + v['cpf'] + ' - Brand: ' + v['brand'] + ' - Friend: ' + v['friend'] + '<br />';
                });
                bodyMsg += radio;
                bodyMsg += '</form><p>Se uno dei prodotti è stato acquistato sarà la scelta obbligata</p>';
                bsModal.writeBody(bodyMsg);
                bsModal.showCancelBtn();
                bsModal.setOkLabel('Fondi');
                bsModal.showOkBtn();
                bsModal.setOkEvent(function () {
                    var choosen = $('input[name="choosen"]:checked').val();
                    bsModal.writeBody("Pensaci un momento. L'azione non è reversibile!");
                    bsModal.setCancelLabel("Ci ho ripensato");
                    bsModal.setOkLabel('Fondi!!!');
                    bsModal.setOkEvent(function () {
                        bsModal.showLoader();
                        Pace.ignore(function () {
                            $.ajax({
                                url: '/blueseal/xhr/ProductMerge',
                                type: 'POST',
                                data: {action: "merge", rows: row, choosen: choosen}
                            }).done(function (res) {
                                bsModal.writeBody(res);
                                bsModal.hideCancelBtn();
                                bsModal.setOkLabel("Ok");
                                bsModal.setOkEvent(function () {
                                    bsModal.hide();
                                    $.refreshDataTable();
                                });
                            });
                        });
                    });
                });
            }
        });
    });
});
