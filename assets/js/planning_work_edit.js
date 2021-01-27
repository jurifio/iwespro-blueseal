(function ($) {
    var planningWorkStatusId = $('#planningWorkStatusIdSelected').val();
    var planningWorkTypeId = $('#planningWorkTypeIdSelected').val();
    var billRegistryClientIdSelected = $('#billRegistryClientIdSelected').val();


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
                },
                onInitialize: function () {
                    var selectize = this;
                    selectize.setValue(planningWorkStatusId);
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
                },
                onInitialize: function () {
                    var selectize = this;
                    selectize.setValue($('#planningWorkTypeIdSelected').val());
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

                }, onInitialize: function () {
                    let selectize = this;
                    selectize.setValue(billRegistryClientIdSelected);
                }
            });
        });

    });
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

})(jQuery);

    $(document).on('bs.post.update', function () {
        let bsModal = new $.bsModal('Salva Attività', {
            body: '<div><p>Premere ok per Salvare' +
                '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {


            start = $('#startDateWork').val();
            end = $('#endDateWork').val();
            const data = {
                planningWorkId: $('#planningWorkId').val(),
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
                url: '/blueseal/xhr/PlanningWorkEditAjaxController',
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

$(document).on('bs.post.view', function () {
    let bsModal = new $.bsModal('Visualizza Email Attività', {
        body: `<div><p>Premere ok per Visualizzare l email allineata allo stato<br>ricordati che per generare la mail deve essere salvata prima con lo stato che interessa
            </div>
            <div class="row">
            <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="toMail">Titolo</label>
                                            <input id="toMail" class="form-control" type="text"
                                                   placeholder="Email" name="toMail"
                                                   value=""
                                                   required="required">
                                        </div>
                                    </div>
</div>
            <div class="row">
            <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="subject"> Note</label>
                                        <textarea class="form-control" name="subject" id="subject"
                                                  value=""></textarea>
                                    </div>
                                </div>
             </div>  
             <div clas="row">
             <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="mail"> Note</label>
                                        <textarea class="form-control" name="mail" id="mail"
                                                  value=""></textarea>
                                    </div>
                                </div>
             </div>  
             
</div>   
    `

    });
    var planningWorkStatusId= $('#planningWorkStatusId').val();
    var planningWorkTypeId=$('#planningWorkTypeId').val();
    var planningWorkId=$('#planningId').val();
    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        start = $('#startDateWork').val();
        end = $('#endDateWork').val();
        const data = {
            planningWorkId: $('#planningWorkId').val(),
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
            method: 'get',
            url: '/blueseal/xhr/PlanningWorkComposeAndSendEmailAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let rawData = res;
            $.each(rawData, function (k, v) {
                    $('#toMail').val(v.toMail);
                    $('#subject').val(v.subject);
                    $('#mail').val(v.text);
            });
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {

                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});






