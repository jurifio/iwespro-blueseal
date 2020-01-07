$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Campaign',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#campaignId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});



$('#campaignId').change(function() {

        var $t = $('table[data-datatable-name]');
        $t.data('campaignId',$('#campaignId').val());
        var dt = $t.DataTable();
        dt.draw();
        $(this).val();


});