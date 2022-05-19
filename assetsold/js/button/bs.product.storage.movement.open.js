window.buttonSetup = {
    tag:"a",
    icon:"fa-exchange",
    permission:"/admin/product/edit",
    event:"bs-product-storage-movement-open",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea movimenti per i prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-storage-movement-open', function(){
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;
    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti di cui creare movimenti"
        }).open();
        return false;
    }

    var i = 0;
    var getVarsArray = [];
    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = v['DT_RowId'];
        i++;
    });

    var getVars = getVarsArray.join(',');

    window.open('/blueseal/prodotti/movimenti/inserisci?code=' + getVars, '_blank');
});