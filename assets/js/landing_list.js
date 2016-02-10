$(document).on('bs.landing.dupe',function(e,element,button) {

});

$(document).on('bs.landing.del', function(e,element,button) {

});

$(document).on('bs.landing.preview', function(e,element,button,parentEvt) {
    parentEvt.preventDefault();

    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;
    var originalHref = element.attr('href');

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno una landing di cui vedere l'anteprima",
            selfClose: false
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi vedere l'anteprima di una sola landing per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        element.attr('href',element.attr('href')+rowId[1]);
        i++;
    });

    window.open(element.attr('href'),'lading-preview');

    element.attr('href',originalHref);
});