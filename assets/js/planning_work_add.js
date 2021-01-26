(function ($) {



    Pace.ignore(function () {

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'PlanningWorkStatus'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#planningWorkStatusId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });

        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'PlanningWorkType'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#planningWorkTypeId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });

        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'billRegistryClient'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#billRegistryClientId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'companyName',
                searchField: 'companyName',
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.companyName) + '</span> - ' +
                            '<span class="caption">contatto:' + escape(item.contactName) + ' tel: ' + escape(item.phoneAdmin) + ' email' + escape(item.emailAdmin) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.companyName) + '</span> - ' +
                            '<span class="caption">contatto:' + escape(item.contactName) + ' tel: ' + escape(item.phoneAdmin) + ' email' + escape(item.emailAdmin) + '</span>' +
                            '</div>'
                    }

                }
            });
        });

    });

})(jQuery);
$('#cost').change(function () {
    let cost=parseFloat($('#cost').val());
    let hour=parseFloat($('#hour').val());

    let netTotalRow=0;

        netTotalRow=cost*hour;

    $('#total').val(netTotalRow.toFixed(2));

});
$('#hour').change(function () {
    let cost=parseFloat($('#cost').val());
    let hour=parseFloat($('#hour').val());

    let netTotalRow=0;

    netTotalRow=cost*hour;

    $('#total').val(netTotalRow.toFixed(2));

});

    $(document).on('bs.post.insert', function () {
        let bsModal = new $.bsModal('Salva Attvità', {
            body: '<div><p>Premere ok per Salvare' +
                '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {


            start = $('#startDateWork').val();
            end = $('#endDateWork').val();
            const data = {
                title: $('#title').val(),
                start: $('#startDateWork').val(),
                end: $('#endDateWork').val(),
                planningWorkStatusId: $('#planningWorkStatusId').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                planningWorkTypeId: $('#planningWorkTypeId').val(),
                request: $('#request').val(),
                solution: $('#solution').val(),
                hour: $('#hour').val(),
                cost: $('#cost').val(),
                percentageStatus: $('#percentageStatus').val(),
                notifyEmail: $('#notifyEmail').val(),


            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PlanningWorkAddAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    window.location.reload();
                    bsModal.hide();
                    // window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });






