var eventColor;
var obj = null;
var typeView = 1;
var facebookCampaignId = '';
var groupInsertionId = '';
var start='';
var end='';
$(document).ready(function () {
    $(this).trigger('bs.load.photo');
    createcalendar(obj, 1);
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/PlanningWorkCalendarFilterAjaxController',
    }).done(function (res) {
        let ret = JSON.parse(res);


        $.each(ret, function (k, v) {
            $('#filterMedia').append('<div style ="background-color:' + v.color + ';"><input type="checkbox" name="' + v.id + '" value="' +v.id + '" /> ' + v.name + '</div>');
        });


    });

});
$('#selectAllSocial').click(function () {
    $('#filterMedia input:checkbox').each(function () {
        if ($(this).is(':checked')) {
            $(this).prop('checked', false);
        } else {
            $(this).prop('checked', true);
        }

    })
});
$('#reload').on('click', function () {
    location.reload();
});
$('#search').on('click', function () {
    $('#calendar').fullCalendar('destroy');


    let checkedSocial = [];
    let checkedSocialName = [];
    $('#filterMedia input:checked').each(function (i) {
        checkedSocial[i] = $(this).val();
        checkedSocialName[i] = $(this).attr('name');
    });


    let url = window.location.href;
    let id = url.substring(url.lastIndexOf('/') + 1);

    const data = {
        planningWorkStatusId: checkedSocial,
        id: id
    };
    $.ajax({
        method: 'POST',
        url: '/blueseal/xhr/PlanningWorkCalendarFilterAjaxController',
        data: data
    }).success(function (data) {

        obj = JSON.parse(data);

        var TypePermission = 1;
        createcalendar(obj, TypePermission);


    }).fail(function (data) {
        alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
        alert("responseText: " + xhr.responseText);
    }).always(function (data) {

    });

});
$('#detailed').on('click', function () {
    $('#calendar').fullCalendar('destroy');
    $('#appendDetailedChekbox').empty();
    $('#appendSinteticChekbox').empty();
    $('#appendDetailedChekbox').append('<i class="fa fa-check"></i>');
    typeView = 1;

    let checkedSocial = [];
    let checkedSocialName = [];
    $('#filterMedia input:checked').each(function (i) {
        checkedSocial[i] = $(this).val();
        checkedSocialName[i] = $(this).attr('name');
    });


    let url = window.location.href;
    let id = url.substring(url.lastIndexOf('/') + 1);

    const data = {
        planningWorkStatusId: checkedSocial,
        id: id
    };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/PlanningWorkCalendarFilterAjaxController',
            data: data
        }).success(function (data) {

            obj = JSON.parse(data);

            var TypePermission = 1;
            createcalendar(obj, TypePermission);


        }).fail(function (data) {
            alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
            alert("responseText: " + xhr.responseText);
        }).always(function (data) {

        });

    });
    $('#detailed').on('click', function () {
        $('#calendar').fullCalendar('destroy');
        $('#appendDetailedChekbox').empty();
        $('#appendSinteticChekbox').empty();
        $('#appendDetailedChekbox').append('<i class="fa fa-check"></i>');
        typeView = 1;

        let checkedSocial = [];
        let checkedSocialName = [];
        $('#filterMedia input:checked').each(function (i) {
            checkedSocial[i] = $(this).val();
            checkedSocialName[i] = $(this).attr('name');
        });


        let url = window.location.href;
        let id = url.substring(url.lastIndexOf('/') + 1);

        const data = {
            planningWorkStatusId: checkedSocial,
            id: id
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/PlanningWorkCalendarListAjaxController',
            data: data
        }).success(function (data) {

            obj = JSON.parse(data);

            var TypePermission = 1;
            createcalendar(obj, TypePermission);


        }).fail(function (data) {
            alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
            alert("responseText: " + xhr.responseText);
        }).always(function (data) {

        });

    });

    $('#sintetic').on('click', function () {
        $('#calendar').fullCalendar('destroy');
        $('#appendDetailedChekbox').empty();
        $('#appendSinteticChekbox').empty();
        $('#appendSinteticChekbox').append('<i class="fa fa-check"></i>');
        typeView = 2;

        let checkedSocial = [];
        let checkedSocialName = [];
        $('#filterMedia input:checked').each(function (i) {
            checkedSocial[i] = $(this).val();
            checkedSocialName[i] = $(this).attr('name');
        });


        let url = window.location.href;
        let id = url.substring(url.lastIndexOf('/') + 1);

        const data = {
            socialId: checkedSocial,
            id: id
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/PlanningWorkCalendarListAjaxController',
            data: data
        }).success(function (data) {

            obj = JSON.parse(data);

            var TypePermission = obj[0].allShops;
            createcalendar(obj, TypePermission);


        }).fail(function (data) {
            alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
            alert("responseText: " + xhr.responseText);
        }).always(function (data) {

        });

    });

    $('#calendar').fullCalendar('destroy');
    let url = window.location.href;
    let id = url.substring(url.lastIndexOf('/') + 1);
    $.ajax({
        url: '/blueseal/xhr/PlanningWorkCalendarListAjaxController',
        type: 'POST',
        async: false,
        data: {id: id},
        success: function (data) {
            obj = JSON.parse(data);
            var TypePermission = 1;
            createcalendar(obj, TypePermission);

        },
        error: function (xhr, err) {
            alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
            alert("responseText: " + xhr.responseText);
        }
    });

    function createcalendar(obj, TypePermission) {
        /* initialize the external events
        -----------------------------------------------------------------*/
        $('#calendar div.calendar').each(function () {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text())
            };
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        });

        /* initialize the calendar
        -----------------------------------------------------------------*/

        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var startDay = now.getFullYear() + "-" + (month) + "-" + (day)+'\T'+("0" + (now.getHours())).slice(-2)+':'+("0" + (now.getMinutes())).slice(-2);
        var endDay = now.getFullYear() + "-" + (month) + "-" + (day)+'\T'+("0" + (now.getHours()+1)).slice(-2)+':'+("0" + (now.getMinutes())).slice(-2);


            var calendar = $('#calendar').fullCalendar({
                lang: 'it',
                //isRTL: true,
                buttonHtml: {
                    prev: '<i class="ace-icon fa fa-chevron-left"></i>',
                    next: '<i class="ace-icon fa fa-chevron-right"></i>'
                },
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                //obj that we get json result from ajax
                events: obj,

                eventRender: function (event, element) {
                    var bgRender = "#ffffff";
                    var bgTitle = "#ffffff";


                    eventColor = "";
                    bgTitle = '<div style="background-color:' + event.color + ';color:black;">';
                    switch (event.planningWorkStatusId) {
                        case "1":
                            bgRender = '<div style="background-color:#f8bb00 ;color:black;">';

                            break;
                        case 2:
                            bgRender = '<div style="background-color:#f22823 ;color:black;"">';

                            break;
                        case 3:
                            bgRender = '<div style="background-color:#fa6801 ;color:black;"">';

                            break;
                        case 4:
                            bgRender = '<div style="background-color:#3e8f3e ;color:black;">';

                            break;
                        case 5:
                            bgRender = '<div style="background-color:#3e8f3e ;color:black;">';

                            break;

                    }


                    if (typeView == 1) {
                        element.find('.fc-title').append(bgTitle +
                            '<b>#' + event.id +
                            ' | ' + event.title +
                            ' | ' + event.companyName +
                            ' | ' + event.request +
                            ' | ' + event.status + '</b></div>' + bgRender +
                            '<br>' +
                            '</div>');
                    } else {
                        element.find('.fc-title').append(bgTitle +
                            '<b>#' + event.id +
                            ' | ' + event.title +
                            ' | ' + event.companyName +
                            ' | ' + event.request +
                            ' | ' + event.status + '</b></div>' + bgRender +
                            '<br>' +
                            '</div>');
                    }


                    //'"<br/><b>Immagine:</b><img width="20px" src="' + event.photoUrl + '"></div>');
                },
                textColor: 'black',
                editable: true,
                selectable: true,
                selectable: true,
                selectHelper: true,

                select: function (start, end, allDay) {
                    let bsModal = $('#bsModal');

                    let header = bsModal.find('.modal-header h4');
                    let body = bsModal.find('.modal-body');
                    let cancelButton = bsModal.find('.modal-footer .btn-default');
                    let okButton = bsModal.find('.modal-footer .btn-success');

                    bsModal.modal();

                    header.html('Carica Foto');
                    okButton.html('Fatto').off().on('click', function () {
                        bsModal.modal('hide');
                        okButton.off();
                    });
                    cancelButton.remove();
                    var photogroup = "";
                    let bodyContent =
                        '<div class="col-md-3">' +
                        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="photoUrl" name="photoUrl" action="POST">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"file\">Immagine Evento</label>' +
                        '<div class=\"fallback\">' +
                        // '<label for=\"file\">Immagine Evento</label>' +
                        '<input name="file" type="file" multiple />' +
                        '</div>' +
                        '</div>' +
                        '</div>' +

                        '</form>';
                    $('#photoUrl').change(function () {
                        photogroup = $('#photoUrl').val();
                    });
                    body.html(bodyContent);

                    var start = $.fullCalendar.formatDate(start, "YYYY-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(end, "YYYY-MM-DD HH:mm:ss");
                    let bsModal1 = new $.bsModal('Invio', {
                        body: '<p>Inserisci  Attività</p>' +
                            `<div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="billRegistryClientId">Seleziona il Cliente </label>
                                            <select id="billRegistryClientId"
                                                    required="required"
                                                    name="billRegistryClientId"
                                                    class="full-width selectpicker"
                                                    placeholder="Selezione il Cliente"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="planningWorkTypeId">Seleziona il tipo di attività </label>
                                            <select id="planningWorkTypeId" name="planningWorkTypeId" required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona lo il tipo di attività"
                                                    data-init-plugin="selectize">
                                                    <option value="10">Realizzazione Post Social</option>
                                                    <option value="11">Eseguita PostProduzione Prodotti</option>
                                                    <option value="12">Creazione Home</option>
                                                    <option value="13">Modifica dettagli prodotti</option>
                                                    <option value="14">Creazione/Modifica saldi</option>
                                                    <option value="15">Prenotazione manuale spedizione</option>
                                                    <option value="16">Statistiche social</option> 
                                                    <option value="17">Statistiche sito</option>
                                                   
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="title">Titolo</label>
                                            <input id="title" class="form-control" type="text"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="notifyEmail">Notificare al Cliente</label>
                                            <select id="notifyEmail" name="notifyEmail" required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona"
                                                    data-init-plugin="selectize">
                                                <option value="0">Non Inviare la Notifica</option>
                                                <option value="1">Invia la Notifica</option>

                                            </select>
                                        </div>
                                    </div>   
                                </div>
                                <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="startDateWork">Data Inizio Attività</label>
                                            <input type="datetime-local" id="startDateWork" class="form-control"
                                                   placeholder="Inserisci la Data di Inizio "
                                                   name="startDateWork" value="` + startDay + `"
                                                   required="required"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="endDateWork">Data Fine Attività </label>
                                            <input type="datetime-local" id="endDateWork" class="form-control"
                                                   placeholder="Inserisci la Data della Fine"
                                                   name="endDateWork" value="` + endDay + `"
                                                   required="required"/>
                                        </div>
                                    </div>
                                <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="planningWorkStatusId">Seleziona lo Stato</label>
                                            <select id="planningWorkStatusId"
                                                    name="planningWorkStatusId" class="full-width selectpicker"
                                                    required="required"
                                                    placeholder="Seleziona lo Stato"
                                                    data-init-plugin="selectize">
                                                    <option value="2"></option>
                                                    <option value="5"></option>
</select>
                                        </div>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="request">Richiesta</label>
                                            <textarea id="request" cols="60" rows="10"
                                                      placeholder="Inserisci la richiesta"
                                                      name="description"></textarea>
                                        </div>
                                    </div>
                             
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="solution">Soluzione</label>
                                            <textarea id="solution" cols="60" rows="10"
                                                      placeholder="Inserisci la soluzione"
                                                      name="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                           <div class="form-group form-group-default selectize-enabled">
                                            <label for="percentageStatus">% di Completamento</label>
                                            <select id="percentageStatus" name="percentageStatus"
                                                    required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona lo il tipo di attività"
                                                    data-init-plugin="selectize">
                                                <option value="10">10%</option>
                                                <option value="20">20%</option>
                                                <option value="30">30%</option>
                                                <option value="40">40%</option>
                                                <option value="50">50%</option>
                                                <option value="60">60%</option>
                                                <option value="70">70%</option>
                                                <option value="80">80%</option>
                                                <option value="90">90%</option>
                                                <option value="100">100%</option>
                                            </select>
                                        </div>
                                        </div>
                                    </div>
                                   <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="hour">Ore lavorate</label>
                                            <input id="hour" class="form-control" type="text"
                                                  name="hour" value="0.00"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="cost">Costo</label>
                                            <input id="cost" class="form-control" type="text" value="0.00"
                                                  name="cost" placeholder="Inserisci il prezzo" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="total">Totale</label>
                                            <input id="total" class="form-control" type="text" value="0"
                                                  name="total" placeholder="totale" />
                                        </div>
                                    </div>
                                </div>
                             <div class="row">   
                            <div class="form-group form-group-default required">
                            <label for="okSend">Invio</label>
                            <div><p>Premere ok per  inserire il dettaglio</p></div>
                            </div>
                            </div>`
                    });


                    bsModal1.addClass('modal-wide');
                    bsModal1.addClass('modal-high');


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

                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'PlanningWorkStatus',

                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#planningWorkStatusId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res2
                        });
                    });
                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'PlanningWorkType',

                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#planningWorkTypeId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res2
                        });
                    });


                    bsModal1.showCancelBtn();
                    bsModal1.setOkEvent(function () {

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
                            type: 'formCalendar',


                        };
                        $.ajax({
                            type: 'POST',
                            url: "/blueseal/xhr/PlanningWorkAddAjaxController",
                            data: data,
                        }).done(function (res) {
                            bsModal1.writeBody('Inserimento eseguito');
                            window.open('/blueseal/planning/modifica/'+res);
                            bsModal1.hide();
                            calendar.fullCalendar('refetchEvents');

                        }).fail(function (res) {
                            bsModal1.writeBody(res);
                        }).always(function (res) {
                            bsModal1.setOkEvent(function () {
                                bsModal1.hide();
                                window.location.reload();

                                // window.location.reload();
                            });
                            bsModal1.showOkBtn();
                        });

                    });


                },
                editable: true,
                eventClick: function (event) {
                    let urlDestination = '/blueseal/planning/modifica/' + event.id;

                    window.open(
                        urlDestination,
                        '_blank'
                    );
                },


                eventDrop:function (event) {


                        let newstart = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                        let newend = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                        var title = event.title;
                        var planningWorkId = event.id;
                        var request = event.request;
                        var solution = event.solution;
                        var cost = event.cost
                        var hour = event.hour;
                        var billRegistryClientId = event.billRegistryClientId;
                        var planningWorkStatusId = event.planningWorkStatusId;
                        var planningWorkTypeId = event.planningWorkTypeId;
                        var percentageStatus = event.percentageStatus;
                        var notifyEmail = event.notifyEmail;

                        $.ajax({
                            url: '/blueseal/xhr/PLanningWorkEditAjaxController',
                            type: 'POST',
                            data: {
                                title: title,
                                start: newstart,
                                end: newend,
                                request: request,
                                solution: solution,
                                billRegistryClientId: billRegistryClientId,
                                planningWorkId: planningWorkId,
                                cost: cost,
                                hour: hour,
                                notifyEmail: notifyEmail,
                                planningWorkStatusId: planningWorkStatusId,
                                planningWorkTypeId: planningWorkTypeId,
                                percentageStatus: percentageStatus
                            },
                            success: function () {
                                calendar.fullCalendar('refetchEvents');
                                alert("Attivita Aggiornata");

                            }
                        });
                    }

                ,
                eventResize: function (event) {
                    let newstart = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    let newend = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    var title = event.title;
                    var planningWorkId = event.id;
                    var request = event.request;
                    var solution = event.solution;
                    var cost = event.cost
                    var hour = event.hour;
                    var billRegistryClientId = event.billRegistryClientId;
                    var planningWorkStatusId = event.planningWorkStatusId;
                    var planningWorkTypeId = event.planningWorkTypeId;
                    var percentageStatus = event.percentageStatus;
                    var notifyEmail = event.notifyEmail;

                    $.ajax({
                        url: '/blueseal/xhr/PlanningWorkEditAjaxController',
                        type: 'POST',
                        data: {
                            title: title,
                            start: newstart,
                            end: newend,
                            request: request,
                            solution: solution,
                            billRegistryClientId: billRegistryClientId,
                            planningWorkId: planningWorkId,
                            cost: cost,
                            hour: hour,
                            notifyEmail: notifyEmail,
                            planningWorkStatusId: planningWorkStatusId,
                            planningWorkTypeId: planningWorkTypeId,
                            percentageStatus: percentageStatus
                        },
                        success: function () {
                            calendar.fullCalendar('refetchEvents');
                            alert("Attivita Aggiornata");

                        }
                    });
                }
                ,

                doubleClick: function (event) {
                    if (confirm("Are you sure you want to remove it?")) {
                        var id = event.id;
                        $.ajax({
                            url: "delete.php",
                            type: "POST",
                            data: {id: id},
                            success: function () {
                                calendar.fullCalendar('refetchEvents');
                                alert("Dettaglio Piano Editoriale Aggiornato");
                            }
                        })
                    }
                },

                eventMouseover: function (event) {


                    tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:#ffffff;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;">' + 'titolo: ' + event.title + '</br>' + 'richiesta: ' + event.request + '</br>' + 'soluzione: ' + event.solution + '</br></div>';


                    $("body").append(tooltip);
                    $(this).mouseover(function (e) {
                        $(this).css('z-index', 10000);
                        $('.tooltiptopicevent').fadeIn('500');
                        $('.tooltiptopicevent').fadeTo('10', 1.9);
                    }).mousemove(function (e) {
                        $('.tooltiptopicevent').css('top', e.pageY + 10);
                        $('.tooltiptopicevent').css('left', e.pageX + 20);
                    });


                },
                eventMouseout: function (data, event, view) {
                    $(this).css('z-index', 8);

                    $('.tooltiptopicevent').remove();

                },

            });



    }
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

(jQuery);


