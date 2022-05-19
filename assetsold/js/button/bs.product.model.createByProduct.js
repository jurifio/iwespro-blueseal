window.buttonSetup = {
    tag:"a",
    icon:"fa-sitemap",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-model-createByProduct",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea nuovo modello da prodotto",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-model-createByProduct', function (e, element, button) {

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (1 != selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare esattamente un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row = {};
        var idsVars = v.DT_RowId.split('-');
        row.id = idsVars[0];
        row.productVariantId = idsVars[1];
        row.name = v.name;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal(
        'creare un modello dal prodotto',
        {
            body: 'Vuoi creare un modello da ' + row.id + '-' + row.productVariantId + '?',
            okButtonEvent: function(){
                window.open('/blueseal/prodotti/modelli/modifica?code=' + row.id + '-' + row.productVariantId, '_blank');
                modal.hide();
            },
            cancelButtonEvent: function(){
                modal.hide();
            },
            isCancelButton: true
        }
    );
});
