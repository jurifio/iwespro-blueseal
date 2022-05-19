window.buttonSetup = {
    tag: "a",
    icon: "fa-book",
    permission: "/admin/product/publish&&allShops",
    event: "bs-set-onlycatalogue",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Solo Catalogo",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-set-onlycatalogue', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    let products = [];
    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
    });


    let bsModal = new $.bsModal("Gestione Prodotti Solo Catalogo", {
        body: `<p>Gestione solo Catalogo</p>
                <div class="form-group form-group-default required">
                 <label for="onlyCatalogue">Solo Catalogo</label>
                 <select id="onlyCatalogue" name="onlyCatalogue">
                 <option value>Seleziona un'opzione</option>
                <option value="1">Si</option>
                <option value="0">No</option>
                 </select> 
                 </div>`
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var onlyCatalogue=$('#onlyCatalogue').val();
        $.ajax({
            url: '/blueseal/xhr/SetOnlyCatalogueAjaxController',
            type: "post",
            data: {
                products: products,
                onlyCatalogue: onlyCatalogue
            },
            dataType:'json'

        }).done(function (res) {
            bsModal.writeBody('Prodotti Aggiornati con Successso!');
            console.error(res);
        }).fail(function (res) {
            modal.writeBody('OOPS! C\'è stato un problema!');
            console.error(res);
        }).always(function () {
            modal.setOkEvent(function () {
                modal.hide();
                dataTable.ajax.reload();
            });
        });

    });
});