window.buttonSetup = {
    tag:"a",
    icon:"fa-file-word-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-viewSize",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Modifica Prezzi",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-viewSize', function(){

    const row = $.getDataTableSelectedRowData();

    if (row){
        let ids = row.split('-');
        let id = ids[0];
        let variant = ids[1];

        $.ajax({
            url: '/blueseal/xhr/getTableContent',
            method: 'GET',
            dataType: 'json',
            data: {
                table: 'ProductSku',
                condition: {
                    productId: id,
                    productVariantId: variant,
                },
                extraFields: ['productSize']
            }
        }).done(function(data){
            window.x = data;
            let val = [];
            for (let d of data){
                val.push(d.productSize.name);
            }
            modal = new $.bsModal(
                'Elenco taglie',
                {
                    body: 'Taglie per SKU:<br />'+val.join("<br />")
                }
            );
        });
    }


});