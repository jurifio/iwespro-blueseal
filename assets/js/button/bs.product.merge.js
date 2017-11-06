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
                body.html(':-( Non posso procedere alla fusione:<br />' + error);
                cancelButton.hide();
                okButton.html('Ok').off().on("click", function () {
                    bsModal.modal("hide");
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
                        if (false == selected) {
                            radio += 'checked';
                            selected = true;
                        }
                    }
                    radio += ' /> ' + v['id'] + '-' + v['productVariantId'] + ' - CPF: ' + v['cpf'] + ' - Brand: ' + v['brand'] + ' - Friend: ' + v['friend'] + '<br />';
                });
                bodyMsg += radio;
                bodyMsg += '</form><p>Se uno dei prodotti è stato acquistato sarà la scelta obbligata</p>';
                body.html(bodyMsg);
                cancelButton.html("Annulla").show().on('click', function () {
                    bsModal.hide();
                });
                okButton.html("Fondi").off().on('click', function () {
                    var choosen = $('input[name="choosen"]:checked').val();
                    body.html("Pensaci un momento. L'azione non è reversibile!");
                    cancelButton.html("Ci ho ripensato");
                    okButton.html("Fondi!").off().on('click', function () {
                        $.ajax({
                            url: '/blueseal/xhr/ProductMerge',
                            type: 'POST',
                            data: {action: "merge", rows: row, choosen: choosen}
                        }).done(function (res) {
                            body.html(res);
                            cancelButton.hide();
                            okButton.html("Ok").off().on('click', function () {
                                bsModal.modal("hide");
                                dataTable.ajax.reload(null, false);
                            });
                        });
                    });
                });
            }
        });
    });
});
