window.buttonSetup = {
    tag: "a",
    icon: "fa-paint-brush",
    permission: "/admin/product/edit",
    event: "bs-product-editVariantDescription",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiorna nome colore produttore",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-editVariantDescription', function (e) {
    e.preventDefault();

    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (0 == selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.DT_RowId;
        i++;
    });


    $.ajax({
        url: "/blueseal/xhr/ProductColorAjaxController",
        type: "GET"
    }).done(function (response) {

        modal = new $.bsModal('Cambia nome produttore',
            {
                body: '<form id="detailAdd"><div class="form-group">' +
                '<p><label>Inserisci il nome variante da assegnare ai prodotti selezionati</label>' +
                '<input type="text" class="form-control new-dett-ita" name="newColorPrice" /></p>' +
                '<p>' +
                response +
                '</p>' +
                '</div></form>',
                isCancelButton: true,
                okButtonEvent: function () {
                    $.ajax({
                        url: '/blueseal/xhr/EditVariantDescription',
                        method: 'post',
                        data: {
                            codes: row,
                            colorNameManufacturer: $('[name="newColorPrice"]').val(),
                            groupId: $('#size-group-select').val(),
                        }
                    }).done(
                        function (res) {
                            modal.writeBody(res);
                            modal.hideCancelBtn();
                            modal.setOkEvent(function () {
                                modal.hide();
                                $.refreshDataTable();
                            });
                        }).fail(function (res) {
                        console.log(res);
                        modal.writeBody('OOPS! ' + res.responseText);
                        modal.hideCancelBtn();
                        modal.setOkEvent(function () {
                            modal.hide();
                        });
                    });
                }
            }
        );

        $('#size-group-select').selectize();
    });
});