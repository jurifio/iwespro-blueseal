window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-lists-manage-brandean",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Gestisci le strategie e i codici Ean  per brand ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-lists-manage-brandean', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    let brands = [];
    $.each(selectedRows, function (k, v) {
        brands.push(v.id);
    });


        let bsModal = new $.bsModal("Gestione", {
            body: `<p>Gestisci le strategie e i codici Ean  per brand</p>
                <div class="form-group form-group-default required">
                 <label for="hasMarketplaceRights">Inserisci su  MarketPlace Prestashop </label>
                 <select id="hasMarketplaceRights" name="hasMarketplaceRights">
                 <option disabled selected value>Seleziona un'opzione</option>
                <option value="1">Si</option>
                <option value="0">No</option>
                 </select> 
                 <div class="form-group form-group-default required">
                 <label for="hasAggregator">Inserisci su  Aggregatori </label>
                 <select id="hasAggregator" name="hasAggregator">
                 <option disabled selected value>Seleziona un'opzione</option>
                <option selected="selected" value="1">Si</option>
                <option value="0">No</option>
                 </select> 
                 </div><div id="otherOptions"></div>`
        });
        $('#hasMarketplaceRights').change(function () {

            let hasMarketplaceRights = $('#hasMarketplaceRights').val();
            let html = "";

            if (hasMarketplaceRights === '1') {
                html = `<div class="form-group form-group-default required">
                    <label for="hasExternalEan">Assegna Ean </label>
                <select id="hasExternalEan" name="hasExternalEan">
                    <option disabled selected value>Seleziona un'opzione</option>
                <option value="1" selected="selected">Si</option>
                    <option value="0">No</option>
                    </select> 
                    </div>`
            } else {
                html = `<div class="form-group form-group-default required">
                 <label for="hasExternalEan">Assegna Ean</label>
                 <select id="hasExternalEan" name="hasExternalEan">
                 <option disabled selected value>Seleziona un'opzione</option>
                <option value="1">Si</option>
                <option value="0">No</option>
                 </select> 
                 </div>`;
            }

            $('#otherOptions').empty().append(html);


        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {



            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ManageProductBrandHasEanListAjaxController',
                data: {
                    brand: brands,
                    hasMarketplaceRight: $('#hasMarketplaceRights').val(),
                    hasAggregator: $('#hasAggregator').val(),
                    hasExternal: $('#hasExternalEan').val()
                }
            }).done(function (res) {
                bsModal.writeBody('Assegnazione completata con successo');
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

});
