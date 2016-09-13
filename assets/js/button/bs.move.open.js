window.buttonSetup = {
    tag:"a",
    icon:"fa-arrow",
    permission:"/admin/product/edit",
    event:"bs.move.open",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Cambia Categoria ai prodotti selezionati",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.move.open', function(){
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
    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = prodId+ i + '=' + v;
        i++;
    });

    var getVars = getVarsArray.join(',');

    window.open('/blueseal/prodotti/movimenti/inserisci?code=098-234,234-234,234-234,', '_blank');
});