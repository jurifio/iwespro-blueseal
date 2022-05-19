var summer = $('textarea.summer');
summer.summernote({
    lang: "it-IT",
    height: 300,
    fontNames: [
        'Arial',
        'Arial Black',
        'Comic Sans MS',
        'Courier',
        'Courier New',
        'Helvetica',
        'Impact',
        'Lucida Grande',
        'Raleway',
        'Serif',
        'Sans',
        'Sacramento',
        'Tahoma',
        'Times New Roman',
        'Verdana'
    ],
    onImageUpload: function (files, editor, welEditable) {
        sendFile(files[0], editor, welEditable);
    },
    fontNamesIgnoreCheck: ['Raleway']
});

function sendFile(file, editor, welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
        cache: false,
        contentType: false,
        processData: false,
        success: function (url) {
            //summer.summernote.editor.insertImage(welEditable, url);
            summer.summernote('pasteHTML', '<p><img src="' + url + '"></p>');
        }
    });
}

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
        let cost = parseFloat($('#cost').val());
        let hour = parseFloat($('#hour').val());

        let netTotalRow = 0;

        netTotalRow = cost * hour;

        $('#total').val(netTotalRow.toFixed(2));

    });
    $('#hour').change(function () {
        let cost = parseFloat($('#cost').val());
        let hour = parseFloat($('#hour').val());

        let netTotalRow = 0;

        netTotalRow = cost * hour;

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
                                            <label for="toMail">Destinatario</label>
                                            <input id="toMail" class="form-control" type="text"
                                                   placeholder="Destinatario" name="toMail"
                                                   value=""
                                                   required="required">
                                        </div>
                                    </div>
</div>
            <div class="row">
             <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="subject">Oggetto</label>
                                        <textarea class="form-control" cols="600" rows="3" name="subject" id="subject"
                                                  value=""></textarea>
                                    </div>
                                </div>
             </div>  
             <div class="row">
             <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="mail">Testo Mail</label>
                                        <textarea class="form-control" cols="600" rows="30" name="mail" id="mail"
                                                  value=""></textarea>
                                    </div>
             </div>
             </div>  
             
             
             
<div class="row" id="appendSend">
             </div>
    `

    });
    var planningWorkStatusId = $('#planningWorkStatusId').val();
    var planningWorkTypeId = $('#planningWorkTypeId').val();
    var planningWorkId = $('#planningId').val();
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
            $('#appendSend').append(`<div class="col-md-12"><button class="success" id="modifyRowInvoiceButton' + counterRowView + '" onclick="sendMail()" type="button"><span class="fa fa-envelope">Invia</span></button></div>`);

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
$(document).on('bs.workevent.view', function () {
    let bsModal = new $.bsModal('Visualizza Email Storico Attività', {
        body: `<div><p>Premere ok per Visualizzare lo storico
            </div>
            <div
<div id="appendList">
             </div>
    `

    });
    var planningWorkStatusId = $('#planningWorkStatusId').val();
    var planningWorkTypeId = $('#planningWorkTypeId').val();
    var planningWorkId = $('#planningId').val();
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
            url: '/blueseal/xhr/PlanningWorkEventListAjaxController',
            data: data,
            dataType: 'json',
        }).done(function (res) {
            console.log(res);
            let rawData = res;
            let rowAppend = '<div class="row"><div class="col-md-2">Stato in Data</div><div class="col-md-1">% Completamento</div><div class="col-md-7">Soluzione</div><div class="col-md-2">Invio Mail</div></div><hr>';
            $.each(rawData, function (k, v) {
                rowAppend = rowAppend + '<div class="row"><div class="col-md-2">' + v.planningWorkStatusName + ' ' + v.dateCreate + '</div><div class="col-md-1">' + v.percentageStatus + '</div><div class="col-md-7">' + v.solution + '</div><div class="col-md-2">' + v.isSent + '</div></div><hr>';
                $('#toMail').val(v.toMail);
                $('#subject').val(v.subject);
                $('#mail').val(v.text);
            });
            $('#appendList').empty();
            $('#appendList').append(rowAppend);

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
$('#notifyEmail').change(function () {
    if ($('#notifyEmail').val() == 1) {
        $('#divprevSend').removeClass('hide');
        $('#divprevSend').addClass('show');
    } else {
        $('#divprevSend').removeClass('show');
        $('#divprevSend').addClass('hide');
    }
});
$(document).on('bs.create.invoice', function () {
    if ($('#planningWorkStatusId').val() == 4 || $('#planningWorkStatusId').val() == 5) {
        let bsModal = new $.bsModal('Genera Fattura', {
            body: `<div><p>Premere ok per Fatturare
            </div>
            <div
<div id="appendList">
             </div>
    `

        });
        var planningWorkStatusId = $('#planningWorkStatusId').val();
        var planningWorkTypeId = $('#planningWorkTypeId').val();
        var planningWorkId = $('#planningId').val();
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
                method: 'post',
                url: '/blueseal/xhr/PlanningWorkComposeInvoiceAjaxController',
                data: data,
                dataType: 'json',
            }).done(function (res) {
                bsModal.writeBody('Inserimento eseguito');
                let url='/blueseal/anagrafica/fatture-modifica?id='+res
                window.location.href=url;
                bsModal.hide();


            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {

                    // window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    } else {
        let bsModal = new $.bsModal('Generazione Fattura', {
            body: `<div><p>Impossibile Generare la fattura per generarla bisogna aver completato l'attività`

        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.close();
        });
    }
});


function sendMail() {

    var planningWorkId = $('#planningWorkId').val();
    var title = $('#title').val();
    var start = $('#startDateWork').val();
    var end = $('#endDateWork').val();
    var planningWorkStatusId = $('#planningWorkStatusId').val();
    var billRegistryClientId = $('#billRegistryClientId').val();
    var planningWorkTypeId = $('#planningWorkTypeId').val();
    var request = $('#request').val();
    var solution = $('#solution').val();
    var hour = $('#hour').val();
    var cost = $('#cost').val();
    var percentageStatus = $('#percentageStatus').val();
    var notifyEmail = $('#notifyEmail').val();
    var toMail = $('#toMail').val();
    var subject = $('#subject').val();
    var mail = $('#mail').val();
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
        toMail: $('#toMail').val(),
        subject: $('#subject').val(),
        mail: $('#mail').val(),
    };
    $.ajax({
        method: 'post',
        url: '/blueseal/xhr/PlanningWorkComposeAndSendEmailAjaxController',
        data: data
    }).done(function (res) {
        $('#appendSend').empty();
        $('#appendSend').append('<div class="col-md-12">' + res + '</div>');

    }).fail(function (res) {
        $('#appendSend').empty();
        $('#appendSend').append('<div class="col-md-12">errore: ' + res + '</div>');
    }).always(function (res) {

    });

}

function previewMail() {
    let bsModal = new $.bsModal('Visualizza Email Attività', {
        body: `<div><p>Premere ok per Visualizzare l email allineata allo stato<br>ricordati che per generare la mail deve essere salvata prima con lo stato che interessa
            </div>
            <div class="row">
            <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="toMail">Destinatario</label>
                                            <input id="toMail" class="form-control" type="text"
                                                   placeholder="Destinatario" name="toMail"
                                                   value=""
                                                   required="required">
                                        </div>
                                    </div>
</div>
            <div class="row">
            <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="subject">Oggetto</label>
                                        <textarea class="form-control" cols="600" rows="3" name="subject" id="subject"
                                                  value=""></textarea>
                                    </div>
            </div>
             </div>  
             <div class="row">
             <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label for="mail">Testo Mail</label>
                                        <textarea class="form-control"cols="600" rows="30" name="mail" id="mail"
                                                  value=""></textarea>
                                    </div>
                                </div>
             </div>  
             
             
             
  
<div class="row" id="appendSend">
             </div>
    `

    });
    var planningWorkStatusId = $('#planningWorkStatusId').val();
    var planningWorkTypeId = $('#planningWorkTypeId').val();
    var planningWorkId = $('#planningId').val();
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
            $('#appendSend').append(`<div class="col-md-12"><button class="success" id="modifyRowInvoiceButton' + counterRowView + '" onclick="sendMail()" type="button"><span class="fa fa-envelope">Invia</span></button></div>`);

        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {

                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });


}






