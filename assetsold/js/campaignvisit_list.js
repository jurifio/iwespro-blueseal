


    $('#campaignId').change(function () {

        var $t = $('table[data-datatable-name]');
        $t.data('campaignId', $('#campaignId').val());
        var dt = $t.DataTable();
        dt.draw();
        $(this).val();


    });

    $(document).ready(function() {
    Pace.ignore(function () {
        var campaignSelect = $('select[name=\"campaignId\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectCampaignAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            campaignSelect.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name + ' ' + item.shop) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name + ' ' + item.shop) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });

});