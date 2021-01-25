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
                    selectize.setValue(planningWorkTypeId);
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




})(jQuery);
var photoUrl = [];
Dropzone.autoDiscover = false;
$(document).ready(function () {

    let dropzone = new Dropzone("#dropzoneModal", {
        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

        maxFilesize: 5,
        maxFiles: 100,
        parallelUploads: 10,
        acceptedFiles: "image/jpeg,video/*",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function (file, xhr, formData) {

        }
    });

    dropzone.on('addedfile', function (file) {
        let urlimage = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
        let filename = file.name;
        let image = urlimage + filename;
        photoUrl.push(image);
    });
    dropzone.on('queuecomplete', function () {
        $(document).trigger('bs.load.photo');
    });
});


$(document).on('bs.post.update', function () {
    let bsModal = new $.bsModal('Salva Post', {
        body: '<div><p>Premere ok per Salvare il Piano Editoriale' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        start = $('#startWorkDate').val();
        end = $('#endWoorkDate').val();
        const data = {
            title: $('#title').val(),
            start: $('#startWorkDate').val(),
            end:$('#endWorkDate').val(),
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

function addCampaign() {
    let bsModal = new $.bsModal('Salva Campagna', {
        body: '<div><p>Premere ok per Salvare la Campagna' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            campaignName: $('#campaignName').val(),
            buying_type: $('#buying_type').val(),
            objective: $('#objective').val(),
            typeBudget: $('#typeBudget').val(),
            lifetime_budget: $('#lifetime_budget').val(),
            editorialPlanId: $('#editorialPlanId').val(),
            groupAdsName: $('#groupAdsName').val()
        }
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CreateFacebookCampaignAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            bsModal.writeBody('Campagna Creata con successo');
            $('#facebookCampaignId').val(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore Nella Creazione della Campagna');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

}

function updateCampaign() {
    let bsModal = new $.bsModal('Salva Campagna', {
        body: '<div><p>Premere ok per Salvare la Campagna' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            campaignId: $('#campaignName').val(),
            buying_type: $('#buying_type').val(),
            objective: $('#objective').val(),
            typeBudget: $('#typeBudget').val(),
            lifetime_budget: $('#lifetime_budget').val(),
            editorialPlanId: $('#editorialPlanId').val()
        }
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/CreateFacebookCampaignAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            bsModal.writeBody('Campagna Aggiornata con successo');
            $('#facebookCampaignId').val(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore Nella Creazione della Campagna');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

}




