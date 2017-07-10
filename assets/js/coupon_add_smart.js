const initTypeSelect = function () {
    Pace.ignore(function () {
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'CouponType'
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#couponTypeId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        var caption = 'tipo: '+item.amountType+', validità:'+item.validity+', minimo spesa:'+item.validForCartTotal;
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>' +
                            ' <span class="caption">' + escape(caption) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        var caption = 'tipo: '+item.amountType+', validità:'+item.validity+', minimo spesa:'+item.validForCartTotal;
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>' +
                            '<span class="caption">' + escape(caption) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
};

$(document).on('bs.coupontype.refresh',function () {
    initTypeSelect();
});

$(document).ready(function () {
   initTypeSelect();
});