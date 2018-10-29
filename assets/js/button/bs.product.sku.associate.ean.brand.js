window.buttonSetup = {
    tag:"a",
    icon:"fa-file-text-o",
    permission:"/admin/product/edit",
    event:"bs-product-sku-associate-ean-brand",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Assegna automaticamente Etichette a Brand",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-sku-associate-ean-brand', function () {



    let bsModal = new $.bsModal('Assegna codice Ean', {
        body: `<p>Associa il brand</p>
                 <div class="col-md-12">
                <div class="form-group form-group-default selectize-enabled"> 
                <label for="Brand" >Seleziona il Brand </label><select id="brand" name="brand" class="full-width selectpicker" placeholder="Selezione il Brand"
                data-init-plugin="selectize"></select> 
                 </div> 
                </div>
                `
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductBrand'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#brand');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });




    bsModal.setOkEvent(function () {
        let brand=$('#brand').val();
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ManageProductSkuAutomaticEan',
            data: {
                p:brand,
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});