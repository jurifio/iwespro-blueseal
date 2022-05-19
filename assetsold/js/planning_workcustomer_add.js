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
                table: 'billRegistryClient',
                condition:{id:$('#billRegistryClientIdSelected').val()}
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

                }, onInitialize: function () {
                    let selectize = this;
                    selectize.setValue($('#billRegistryClientIdSelected').val());
                }

            });
        });

    });

})(jQuery);
$('#planningWorkTypeId').change(function () {
    $('#title').val($('#planningWorkTypeId :selected').text())

});

    $(document).on('bs.post.insert', function () {
        let bsModal = new $.bsModal('Carica richiesta', {
            body: '<div><p>Premere ok per Inviarla' +
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
                type: 'formAdd',


            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PlanningWorkCustomerAddAjaxController',
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






