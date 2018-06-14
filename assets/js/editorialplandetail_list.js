(function ($) {
    var obj = null;
    $(document).ready(function () {
        $(this).trigger('bs.load.photo');
        createcalendar(obj, 1);
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/EditorialPlanSocialFilterAjaxController',
        }).done(function (res) {
            let ret = JSON.parse(res);


            $.each(ret.social, function (k, v) {
                $('#filterMedia').append('<div><input type="checkbox" name="' + v + '" value="' + k + '" /> ' + v + '</div>');
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
            socialId: checkedSocial,
            id: id
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/EditorialPlanDetailListFilteredAjaxController',
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
        url: '/blueseal/xhr/EditorialPlanDetailListAjaxController',
        type: 'POST',
        async: false,
        data: {id: id},
        success: function (data) {
            obj = JSON.parse(data);
            var TypePermission = obj[0].allShops;
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
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        if (TypePermission == "1") {


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
                    let bgRender = "#ffffff";
                    switch (event.status) {
                        case "Bozza":
                            bgRender = '<div style="background-color:darkblue>';
                            break;
                        case "Approvata":
                            bgRender = '<div style="background-color:darkorange">';
                            break;
                        case "Rifiutata":
                            bgRender = '<div style="background-color:red">';
                            break;
                        case "Pubblicata":
                            bgRender = '<div style="background-color:green">';
                            break;

                    }
                    var linkimg="";
                    var link  =event.photoUrl.split(",");
                    link.forEach(function(element) {
                        linkimg=linkimg + '<br/><b>Immagine:</b><img width="20px" src="' + element + '">';
                    });
                    element.find('.fc-title').append(bgRender + '<br/><b>Descrizione:</b>' + event.description +
                        '"<br/><b>Argomento:</b>"' + event.argumentName +
                        '"<br/><b>Piano Editoriale:</b>"' + event.titleEditorialPlan +
                        '"<br/><b>Media utilizzato:</b>"' + event.socialName +
                        '"<br/><b>Stato:</b>"' + event.status +
                        '"<br/><b>Note:</b>"' + event.note + linkimg+
                        '</div>');


                        //'"<br/><b>Immagine:</b><img width="20px" src="' + event.photoUrl + '"></div>');
                },
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
                    var photogroup="";
                    let bodyContent =
                        '<div class="col-md-3">' +
                        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="photoUrl" name="photoUrl" action="POST">'+
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"file\">Immagine Evento</label>' +
                        '<div class=\"fallback\">' +
                       // '<label for=\"file\">Immagine Evento</label>' +
                        '<input name="file" type="file" multiple />' +
                        '</div>' +
                        '</div>' +
                        '</div>' +

                        '</form>';
                    $('#photoUrl').change(function() {
                        photogroup =$('#photoUrl').val();
                    });
                    body.html(bodyContent);

                    var start = $.fullCalendar.formatDate(start, "DD-MM-YYYY hh:mm:ss");
                    var end = $.fullCalendar.formatDate(end, "DD-MM-YYYY hh:mm:ss");
                    let bsModal1 = new $.bsModal('Invio', {
                        body: '<p>Inserisci un Evento per il Piano Editoriale</p>' +
                        '<div class=\"row\">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group form-group-default selectize-enabled">' +
                        '<label for="editorialPlanArgumentId">Argomento Evento</label>' +
                        '<select id="editorialPlanArgumentId"' +
                        ' name="editorialPlanArgumentId" class="full-width selectpicker"' +
                        ' required="required"' +
                        ' placeholder="Selezione l\'argomento da utilizzare"' +
                        ' data-init-plugin="selectize"></select>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleEditorialPlanArgument\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleEditorialPlanArgument\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isVisibleEditorialPlanArgument\" ">' +
                        '</div>' +
                        '</div>' +

                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"titleEvent\">Titolo Azione Evento</label>' +
                        '<input id=\"titleEvent\" class=\"form-control\"' +
                        'placeholder=\"Inserisci il titolo\" name=\"titleEvent\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isEventVisible\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isEventVisible\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isEventVisible\" ">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"description\">Descrizione Evento</label>' +
                        '<input id=\"description\" class=\"form-control\"' +
                        'placeholder=\"Inserisci la descrizione \" name=\"description\" ">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleDescription\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleDescription\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isVisibleDescription\" ">' +
                        '</div>' +
                        '</div>' +
                            bodyContent+
                        /*'<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
                        '<div class="fallback">'+
                        '<input name="photoUrl" id="photoUrl" type="file" multiple />' +
                        '</div>' +
                        '</form>'+*/
                       /* '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"photoUrl\">Immagine Evento</label>' +
                        '<input type=\"text\" id=\"photoUrl\" class=\"form-control\"' +
                        'placeholder=\"Inserisci il link immagine \" name=\"photoUrl\" ">' +
                        '</div>' +
                        '</div>' +*/
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisiblePhotoUrl\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisiblePhotoUrl\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isVisiblePhotoUrl\" ">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-9\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"bodyEvent\">Testo Evento</label>' +
                        '<textarea id="bodyEvent" cols="150" rows="10" name="bodyEvent" placeholder="Inserisci il testo"></textarea>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleBodyEvent\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleBodyEvent\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isVisibleBodyEvent\" ">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"note\">Note Evento </label>' +
                        '<textarea id="note" cols="100" rows="10" name="note" placeholder="Inserisci le note"></textarea>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleNote\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleNote\" class=\"form-control\"' +
                        'placeholder=\"Visible\" checked="true" name=\"isVisibleNote\" ">' +
                        '</div>' +
                        '</div>' +
                        ' <div class="col-md-3">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"socialPlanId\">Seleziona il media da Associare </label><select id=\"socialPlanId\"  required=\"required\" name=\"socialPlanId\" class=\"full-width selectpicker\" placeholder=\"Selezione il media da associare\"' +
                        'data-init-plugin=\"selectize\"></select>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"startEventDate\">Data  Inizio Evento </label>' +
                        '<input  type =\'datetime-local\' id=\"startEventDate\" class=\"form-control\"' +
                        'placeholder=\"Inserisci la Data di Inizio del Dettaglio\" name=\"startEventDate\" value=\"' + start + '\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"endEventDate\">Data Fine Evento </label>' +
                        '<input  type =\'datetime-local\' id=\"EndEventDate\" class=\"form-control\"' +
                        'placeholder=\"Inserisci la Data della Fine del Dettaglio  \" name=\"endEventDate\" value=\"' + end + '\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<div class="form-group form-group-default selectize-enabled">' +
                        '<label for="status">Seleziona lo Stato</label>' +
                        '<select id="status" name="status" required="required"' +
                        'class="full-width selectpicker"' +
                        'placeholder="Seleziona lo stato"' +
                        'data-init-plugin="selectize">' +
                        '<option value="new">Stato</option>' +
                        '<option value="Draft">Bozza</option>' +
                        '<option value="Approved">Approvata</option>' +
                        '<option value="Rejected">Rifiutata</option>' +
                        '<option value="Published">Pubblicata</option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<div class="form-group form-group-default selectize-enabled">' +
                        '<label for="notifyEmail">Notificare al Cliente</label>' +
                        '<select id="notifyEmail" name="notifyEmail" required="required"' +
                        'class="full-width selectpicker"' +
                        'placeholder="Seleziona"' +
                        'data-init-plugin="selectize">' +
                        '<option value="">Seleziona</option>' +
                        '<option value="yesNotify">Invia la Notifica</option>' +
                        '<option value="notNotify">Non Inviare la Notifica</option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-group form-group-default required">' +
                        '<label for="okSend">Invio</label>' +
                        '<div><p>Premere ok per  inserire il dettaglio</p></div>' +
                        '</div>' +
                        '<input type="hidden" id="editorialPlanId" name="editorialPlanId" value=\"' + id + '\"/>'
                    });


                    bsModal1.addClass('modal-wide');
                    bsModal1.addClass('modal-high');

                    $('#isEventVisible').prop('checked', true);
                    $('#isVisibleEditorialPlanArgument').prop('checked', true);
                    $('#isVisibleDescription').prop('checked', true);
                    $('#isVisibleNote').prop('checked', true);
                    $('#isVisibleBodyEvent').prop('checked', true);
                    $('#isVisiblePhotoUrl').prop('checked', true);


                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlanSocial',

                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#socialPlanId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res2,
                        });
                    });
                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlanArgument',
                            condition: {type: 1},
                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#editorialPlanArgumentId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'titleArgument',
                            searchField: 'titleArgument',
                            options: res2,
                        });
                    });


                    bsModal1.showCancelBtn();
                    bsModal1.setOkEvent(function () {
                        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
                        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
                        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
                        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
                        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
                        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
                        const data = {
                            title: $('#titleEvent').val(),
                            start: start,
                            end: end,
                            argument: $('#editorialPlanArgumentId').val(),
                            description: $('#description').val(),
                            note: $('#note').val(),
                            isVisibleNote: isVisNote,
                          //  photoUrl: photogroup,
                            status: $('#status').val(),
                            socialId: $('#socialPlanId').val(),
                            editorialPlanId: $('#editorialPlanId').val(),
                            notifyEmail: $('#notifyEmail').val(),
                            isEventVisible: isEvVisible,
                            isVisibleEditorialPlanArgument: isVisEdPlanArg,
                            isVisiblePhotoUrl: isVisPhoto,
                            isVisibleDescription: isVisDesc,
                            bodyEvent: $('#bodyEvent').val(),
                            isVisibleBodyEvent: isVisBody,


                        };
                        $.ajax({
                            type: 'POST',
                            url: "/blueseal/xhr/EditorialPlanDetailAddAjaxController",
                            data: data,
                        }).done(function (res) {
                            bsModal1.writeBody(res);
                        }).fail(function (res) {
                            bsModal1.writeBody(res);
                        }).always(function (res) {
                            bsModal1.setOkEvent(function () {
                                window.location.reload();
                                bsModal1.hide();
                                // window.location.reload();
                            });
                            bsModal1.showOkBtn();
                        });

                    });
                    let dropzone = new Dropzone("#dropzoneModal",{
                        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

                        maxFilesize: 5,
                        maxFiles: 100,
                        parallelUploads: 10,
                        acceptedFiles: "image/jpeg",
                        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
                        uploadMultiple: true,
                        sending: function(file, xhr, formData) {
                        }
                    });

                    dropzone.on('addedfile',function(){
                        okButton.attr("disabled", "disabled");
                    });
                    dropzone.on('queuecomplete',function(){
                        okButton.removeAttr("disabled");
                        $(document).trigger('bs.load.photo');
                    });


                },
                editable: true,
                eventClick: function (event) {

                    var title = event.title;
                    var editorialPlanDetailId = event.id;
                    var argument = event.argument;
                    var description = event.description;
                    var isVisibleDescription = event.isVisibleDescription;
                    var isEventVisible = event.isEventVisible;
                    var isVisibleEditorialPlanArgument = event.isVisibleEditorialPlanArgument;
                    var isVisibleNote = event.isVisibleNote;
                    var bodyEvent = event.bodyEvent;
                    var isVisibleBodyEvent = event.isVisibleBodyEvent;
                    var isVisiblePhotoUrl = event.isVisiblePhotoUrl;
                    var photoUrl = event.photoUrl;
                    var status = event.status;
                    var selectedDraft = "";
                    var selectedApproved = "";
                    var selectedRejected = "";
                    var selectedPublished = "";
                    if (status === 'Draft') {
                        selectedDraft = 'selected=selected';
                    }
                    if (status === 'Approved') {
                        selectedApproved = 'selected=selected';
                    }
                    if (status === 'Rejected') {
                        selectedRejected = 'selected=selected';
                    }
                    if (status === 'Published') {
                        selectedPublished = 'selected=selected';
                    }

                    var argumentName = event.argumentName;
                    let url1 = window.location.href;
                    let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);

                    var note = event.note;
                    var socialId = event.socialId;
                    var socialName = event.socialName;
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
                    var linkimg="";
                    var link  =event.photoUrl.split(",");
                    link.forEach(function(element) {
                        linkimg=linkimg + '<br/><img width="150px" src="' + element + '">';
                    });


                    var photogroup="";
                    let bodyContent =
                        '<div class="col-md-3">' +
                        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="photoUrl" name="photoUrl" action="POST">'+
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"file\">Immagine Evento'+linkimg+'</label>' +
                        '<div class=\"fallback\">' +
                        //'<label for=\"file\">Immagine Evento'+linkimg+'</label>' +
                        '<input name="file" type="file" multiple />' +
                        '</div>' +
                        '</div>' +
                        '</div>' +

                        '</form>';
                    $('#photoUrl').change(function() {
                        photogroup =$('#photoUrl').val();
                    });
                    body.html(bodyContent);


                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    let bsModal2 = new $.bsModal('Invio', {
                        body: '<p>Modifica l\'evento per il Piano Editoriale</p>' +
                        '<div class=\"row\">' +
                        '<div class="col-xs-3">' +
                        '<div class="form-group form-group-default selectize-enabled">' +
                        '<label for="editorialPlanArgumentId">Argomento Evento</label>' +
                        '<select id="editorialPlanArgumentId" name="editorialPlanArgumentId" class="full-width selectpicker"' +
                        'placeholder="Selezione l\' argomento da utilizzare"' +
                        'data-init-plugin="selectize">' +
                        '<option  value="' + argument + '">' + argumentName + '</option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleEditorialPlanArgument\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleEditorialPlanArgument\" class=\"form-control\"' +
                        'placeholder=\"Visible\"  name=\"isVisibleEditorialPlanArgument\" ">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"\">titolo Evento</label>' +
                        '<input id=\"titleEvent\" class=\"form-control\"' +
                        'placeholder=\"Modifica il titolo\" name=\"titleEvent\" value=\"' + title + '\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isEventVisible\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isEventVisible\" class=\"form-control\"' +
                        'placeholder=\"Visible\" name=\"isEventVisible\" ">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"description\">Descrizione Evento</label>' +
                        '<input id=\"description\" class=\"form-control\"' +
                        'placeholder=\"Modifica la descrizione \" name=\"description\" value=\"' + description + '\">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleDescription\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleDescription\" class=\"form-control\"' +
                        'placeholder=\"Visible\" name=\"isVisibleDescription\" ">' +
                        '</div>' +
                        '</div>' +  bodyContent +
                      /*  '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"photoUrl\">Immagine Evento'+linkimg+'</label>' +
                        '<input id=\"photoUrl\" class=\"form-control\"' +
                        'placeholder=\"Inserisci il link immagine \" name=\"photoUrl\" value=\"' + photoUrl + '\">' +
                        '</div>' +
                        '</div>' +*/
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisiblePhotoUrl\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisiblePhotoUrl\" class=\"form-control\"' +
                        'placeholder=\"Visible\" name=\"isVisiblePhotoUrl\">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-9\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"bodyEvent\">Testo Evento</label>' +
                        '<textarea id="bodyEvent" cols="150" rows="10" name="bodyEvent">' + bodyEvent + '</textarea>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleBodyEvent\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleBodyEvent\" class=\"form-control\"' +
                        'placeholder=\"Visible\"  name=\"isVisibleBodyEvent\">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"note\">Note Evento</label>' +
                        '<textarea id="note" cols="100" rows="10" name="note" placeholder="Inserisci le note">' + note + '</textarea>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-3\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"isVisibleNote\">Visibile</label>' +
                        '<input  type="checkbox" id=\"isVisibleNote\" class=\"form-control\"' +
                        'placeholder=\"Visible\"  name=\"isVisibleNote\" ">' +
                        '</div>' +
                        '</div>' +
                        ' <div class="col-md-3">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"socialPlanId\">Seleziona il media da Associare </label><select id=\"socialPlanId\"  required=\"required\"   name=\"socialPlanId\" class=\"full-width selectpicker\" placeholder=\"Seleziona il media da Associare\"' +
                        'data-init-plugin=\"selectize\">' +
                        '<option value="' + socialId + '">' + socialName + '</option>' +
                        '</select>' +
                        ' </div>' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"row\">' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"startEventDate\">Data Inizio Evento </label>' +
                        '<input  type =\'datetime\' id=\"startEventDate\" class=\"form-control\"' +
                        'placeholder=\"Modifica la Data di Inizio del Dettaglio\" name=\"startEventDate\" value=\"' + start + '\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '<div class=\"col-md-6\">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"endEventDate\">Data Fine Evento </label>' +
                        '<input  type =\'datetime\' id=\"EndEventDate\" class=\"form-control\"' +
                        'placeholder=\"Modifica la Data della Fine del Dettaglio  \" name=\"endEventDate\" value=\"' + end + '\" required=\"required\">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<div class="form-group form-group-default selectize-enabled">' +
                        '<label for="status">Seleziona lo Stato</label>' +
                        '<select id="status" name="status" required="required"' +
                        'class="full-width selectpicker"' +
                        'placeholder="Seleziona lo stato"' +
                        'data-init-plugin="selectize">' +
                        '<option ' + selectedDraft + ' value="Draft">Bozza</option>' +
                        '<option ' + selectedApproved + ' value="Approved">Approvata</option>' +
                        '<option ' + selectedRejected + ' value="Rejected">Rifiutata</option>' +
                        '<option ' + selectedPublished + ' value="Published">Pubblicata</option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<div  class="form-group form-group-default selectize-enabled">' +
                        '<label for="notifyEmail">Notificare al Cliente</label>' +
                        '<select id="notifyEmail" name="notifyEmail" required="required"' +
                        'class="full-width selectpicker"' +
                        'placeholder="Seleziona"' +
                        'data-init-plugin="selectize">' +
                        '<option value="">Seleziona</option>' +
                        '<option value="yesNotify">Invia la Notifica</option>' +
                        '<option value="notNotify">Non Inviare la Notifica</option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-group form-group-default required">' +
                        '<label for="okSend">Modifica</label>' +
                        '<div><p>Premere ok per  inserire il dettaglio</p></div>' +
                        '</div>' +
                        '<div class="form-group form-group-default required">' +
                        '<label for="deleteDetail">Cancellazione</label>' +
                        '<div><p>Cancella il Dettaglio</p></div>' +
                        '<input type="button" class="btn-success" id="deleteDetail" name="deleteDetail" value="Cancella il Dettaglio del Piano"' +
                        '</div>' +
                        '<input type="hidden" id="editorialPlanId" name="editorialPlanId" value=\"' + editorialPlanId + '\"/>' +
                        '<input type="hidden" id="editorialPlanDetailId" name="editorialPlanDetailId" value=\"' + editorialPlanDetailId + '\"/>'
                    });
                    bsModal2.addClass('modal-wide');
                    bsModal2.addClass('modal-high');
                    let dropzone = new Dropzone("#dropzoneModal",{
                        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

                        maxFilesize: 5,
                        maxFiles: 100,
                        parallelUploads: 10,
                        acceptedFiles: "image/jpeg",
                        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
                        uploadMultiple: true,
                        sending: function(file, xhr, formData) {
                        }
                    });

                    dropzone.on('addedfile',function(){
                        okButton.attr("disabled", "disabled");
                    });
                    dropzone.on('queuecomplete',function(){
                        okButton.removeAttr("disabled");
                        $(document).trigger('bs.load.photo');
                    });

                    if (isEventVisible == "1") {
                        $('#isEventVisible').prop('checked', true);
                    } else {
                        $('#isEventVisible').prop('checked', false);
                    }
                    if (isVisibleEditorialPlanArgument == "1") {
                        $('#isVisibleEditorialPlanArgument').prop('checked', true);

                    } else {
                        $('#isVisibleEditorialPlanArgument').prop('checked', false);
                    }
                    if (isVisibleDescription == "1") {
                        $('#isVisibleDescription').prop('checked', true);

                    } else {
                        $('#isVisibleDescription').prop('checked', false);
                    }
                    if (isVisibleNote == "1") {
                        $('#isVisibleNote').prop('checked', true);
                    } else {
                        $('#isVisibleNote').prop('checked', false);
                    }
                    if (isVisibleBodyEvent == "1") {
                        $('#isVisibleBodyEvent').prop('checked', true);

                    } else {
                        $('#isVisibleBodyEvent').prop('checked', false);
                    }

                    if (isVisiblePhotoUrl == "1") {
                        $('#isVisiblePhotoUrl').prop('checked', true);

                    } else {
                        $('#isVisiblePhotoUrl').prop('checked', false);
                    }


                    $("#deleteDetail").click(function () {
                        if (confirm("Sei Sicuro di Cancellare il Dettaglio del Piano Editoriale")) {
                            /*   var title = event.title;
                               var editorialPlanDetailId = event.id;
                               var startEventDate = event.start;
                               var endEventDate = event.end;
                               var argument = event.argument;
                               var description = event.description;
                               var photoUrl = event.photoUrl;
                               var status = event.status;
                               var note = event.note;
                               var notify socialId = event.socialId;
           */
                            var url1 = window.location.href;
                            var editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);
                            $.ajax({
                                    url: "/blueseal/xhr/EditorialPlanDetailEditAjaxController",
                                    type: "put",
                                    data: {
                                        editorialPlanId: editorialPlanId,
                                        editorialPlanDetailId: editorialPlanDetailId,
                                        argument: argument,
                                        description: description,
                                       // photoUrl: photoUrl,
                                        status: status,
                                        socialId: socialId,
                                        title: title,
                                        note: note,


                                    },


                                    //  data: {editorialPlanDetailId:editorialPlanDetailId},
                                    success: function (res) {
                                        alert(res);
                                        calendar.fullCalendar('refetchEvents');

                                    }

                                }
                            );
                            bsModal2.hide();
                            window.location.reload();
                        }
                    });
                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlanSocial',

                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#socialPlanId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res2,
                        });
                    });
                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlanArgument',
                            selection: {id: argument},
                            condition: {type: 1},

                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#editorialPlanArgumentId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'titleArgument',
                            searchField: 'titleArgument',
                            options: res2,
                        });
                    });


                    bsModal2.showCancelBtn();
                    bsModal2.setOkEvent(function () {
                        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
                        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
                        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
                        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
                        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
                        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
                        const data = {
                            title: $('#titleEvent').val(),
                            start: start,
                            end: end,
                            argument: $('#editorialPlanArgumentId').val(),
                            description: $('#description').val(),
                            note: $('#note').val(),
                            isVisibleNote: isVisNote,
                           // photoUrl: $('#photoUrl').val(),
                            status: $('#status').val(),
                            socialId: $('#socialPlanId').val(),
                            editorialPlanId: $('#editorialPlanId').val(),
                            editorialPlanDetailId: $('#editorialPlanDetailId').val(),
                            notifyEmail: $('#notifyEmail').val(),
                            isEventVisible: isEvVisible,
                            isVisibleEditorialPlanArgument: isVisEdPlanArg,
                            isVisibleDescription: isVisDesc,
                            isVisiblePhotoUrl: isVisPhoto,
                            bodyEvent: $('#bodyEvent').val(),
                            isVisibleBodyEvent: isVisBody


                        };


                        $.ajax({
                            type: 'POST',
                            url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
                            data: data
                        }).done(function (res) {
                            bsModal2.writeBody(res);
                        }).fail(function (res) {
                            bsModal2.writeBody(res);
                        }).always(function (res) {
                            bsModal2.setOkEvent(function () {
                                window.location.reload();
                                bsModal2.hide();
                                // window.location.reload();
                            });
                            bsModal2.showOkBtn();
                        });

                    });



                },

                eventDrop:

                    function (event) {
                        var newstart = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                        var newend = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

                        var title = event.title;
                        var isEventVisible = event.isEventVisible;
                        var editorialPlanDetailId = event.id;
                        var argument = event.argument;
                        var isVisibleEditorialPlanArgument = event.isVisibleEditorialPlanArgument;
                        var description = event.description;
                        var isVisibleDescription = event.isVisibleDescription;
                    //    var photoUrl = event.photoUrl;
                        var isVisiblePhotoUrl = event.isVisiblePhotoUrl;
                        var status = event.status;
                        switch (status) {
                            case "Bozza":
                                status = "Draft";
                                break;
                            case "Approvata":
                                status = "Approved";
                                break;
                            case "Rifiutata":
                                status = "Rejected";
                                break;
                            case "Pubblicata":
                                status = "Published";
                                break;
                        }
                        var note = event.note;
                        var isVisibleNote = event.isVisibleNote;
                        var bodyEvent = event.bodyEvent;
                        var isVisibleBodyEvent = event.isVisibleBodyEvent;
                        var socialId = event.socialId;
                        var notifyEmail = "yesNotify";

                        let url1 = window.location.href;
                        let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);
                        $.ajax({
                            url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
                            type: 'POST',
                            data: {
                                title: title,
                                isEventVisible: isEventVisible,
                                start: newstart,
                                end: newend,
                                note: note,
                                isVisibleNote: isVisibleNote,
                                editorialPlanId: editorialPlanId,
                                editorialPlanDetailId: editorialPlanDetailId,
                                argument: argument,
                                isVisibleEditorialPlanArgument: isVisibleEditorialPlanArgument,
                                isVisiblePhotoUrl: isVisiblePhotoUrl,
                                description: description,
                                isVisibleDescription: isVisibleDescription,
                               // photoUrl: photoUrl,
                                status: status,
                                bodyEvent: bodyEvent,
                                isVisibleBodyEvent: isVisibleBodyEvent,
                                socialId: socialId,
                                notifyEmail: notifyEmail
                            },
                            success: function () {
                                calendar.fullCalendar('refetchEvents');
                                alert("Dettaglio Piano Editoriale Aggiornato");

                            }
                        });
                    }

                ,
                eventResize: function (event) {
                    let newstart = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    let newend = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

                    var title = event.title;
                    var isEventVisible = event.isEventVisible;
                    var editorialPlanDetailId = event.id;
                    var argument = event.argument;
                    var isVisibleEditorialPlanArgument = event.isVisibleEditorialPlanArgument;
                    var description = event.description;
                    var isVisibleDescription = event.isVisibleDescription;
                    var photoUrl = event.photoUrl;
                    var isVisiblePhotoUrl = event.isVisiblePhotoUrl;
                    var status = event.status;
                    switch (status) {
                        case "Bozza":
                            status = "Draft";
                            break;
                        case "Approvata":
                            status = "Approved";
                            break;
                        case "Rifiutata":
                            status = "Rejected";
                            break;
                        case "Pubblicata":
                            status = "Published";
                            break;
                    }
                    var note = event.note;
                    var isVisibleNote = event.isVisibleNote;
                    var bodyEvent = event.bodyEvent;
                    var isVisibleBodyEvent = event.isVisibleBodyEvent;
                    var socialId = event.socialId;
                    var notifyEmail = "yesNotify";

                    let url1 = window.location.href;
                    let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);
                    $.ajax({
                        url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
                        type: 'POST',
                        data: {
                            title: title,
                            isEventVisible: isEventVisible,
                            start: newstart,
                            end: newend,
                            note: note,
                            isVisibleNote: isVisibleNote,
                            editorialPlanId: editorialPlanId,
                            editorialPlanDetailId: editorialPlanDetailId,
                            argument: argument,
                            isVisibleEditorialPlanArgument: isVisibleEditorialPlanArgument,
                            isVisiblePhotoUrl: isVisiblePhotoUrl,
                            description: description,
                            isVisibleDescription: isVisibleDescription,
                           // photoUrl: photoUrl,
                            status: status,
                            bodyEvent: bodyEvent,
                            isVisibleBodyEvent: isVisibleBodyEvent,
                            socialId: socialId,
                            notifyEmail: notifyEmail
                        },
                        success: function () {
                            calendar.fullCalendar('refetchEvents');
                            alert("Dettaglio Piano Editoriale Aggiornato");

                        }
                    });
                }
                ,

                eventDoubleClick: function (event) {
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
                }
                ,

            });
        } else {
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
                textColor: 'black',
                eventRender: function (event, element) {
                    let visibleArgument;
                    let visibleDescription;
                    let visiblePhotoUrl;
                    let visibleBodyEvent;
                    let visibleNote;
                    if (event.isVisibleDescription === "1") {
                        visibleDescription = "<br/><b>Descrizione:</b>" + event.description;
                    } else {
                        visibleDescription = "";

                    }
                    if (event.isVisibleEditorialPlanArgument === "1") {
                        visibleArgument = "<br/><b>Argomento:</b>" + event.argumentName;
                    } else {
                        visibleArgument = "";

                    }
                    if (event.isVisiblePhotoUrl === "1") {
                        visiblePhotoUrl = "<br/><b>Immagine:</b>" + event.photoUrl;
                    } else {
                        visiblePhotoUrl = "";

                    }
                    if (event.isVisibleNote === "1") {
                        visibleNote = "<br/><b>Argomento:</b>\"Note:</b>" + event.note;
                    } else {
                        visibleNote = "";

                    }
                    let bgRender = "#ffffff";
                    switch (event.status) {
                        case "Bozza":
                            bgRender = '<div style="background-color:darkblue>';
                            break;
                        case "Approvata":
                            bgRender = '<div style="background-color:darkorange">';
                            break;
                        case "Rifiutata":
                            bgRender = '<div style="background-color:red">';
                            break;
                        case "Pubblicata":
                            bgRender = '<div style="background-color:green">';
                            break;

                    }


                    if (event.isEventVisible === "1") {

                        element.find('.fc-title').append(bgRender + visibleDescription + visibleArgument +
                            '"<br/><b>Piano Editoriale:</b>"' + event.titleEditorialPlan +
                            '"<br/><b>Media utilizzato:</b>"' + event.socialName +
                            '"<br/><b>Stato:</b>' + event.status + visibleNote + visiblePhotoUrl + "</div>");
                    } else {
                        element.find('.fc-content').hide();
                        element.find('.fc-title').hide();


                    }
                },
                editable: true,
                selectable: true,
                selectable: true,
                selectHelper: true,
                editable: true,


            });


        }
    }

})
(jQuery);

