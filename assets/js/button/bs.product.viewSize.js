window.buttonSetup = {
    tag:"a",
    icon:"fa-dollar",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-viewSize",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Modifica Prezzi",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-viewSize', function(){

    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if ((!selectedRowsCount) || ('' == selectedRowsCount || selectedRowsCount == 1 )){
        var row = {};
        var id;
        var variant;
        $.each(selectedRows, function (k, v) {
            var idsVars = v.DT_RowId.split('-');
            row.id = idsVars[0];
            row.productVariantId = idsVars[1];
            id = row.id;
            variant = row.productVariantId;
        });


        var d ="aaa";

        $.ajax({
            method: 'post',
            url: "/blueseal/xhr/ViewSizeProduct",
            data: {
                passId: id,
                passVariant: variant
            }
        }).done(function(data){
            window.x = data;
        });
    }


});