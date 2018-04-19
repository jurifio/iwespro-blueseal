(function ($) {

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

        },
        error: function (xhr, err) {
            alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
            alert("responseText: " + xhr.responseText);
        }
    });

    /* initialize the external events
    -----------------------------------------------------------------*/
    $('#calendar div.calendar').each(function () {
        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
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
        events: obj
        ,
        editable: true,
        selectable: true,
        selectable: true,
        selectHelper: true,
        select: function (start, end, allDay) {
            var start = $.fullCalendar.formatDate(start, "DD-MM-YYYY hh:mm:ss");
            var end = $.fullCalendar.formatDate(end, "DD-MM-YYYY hh:mm:ss");
            let bsModal = new $.bsModal('Invio', {
                body: '<p>Inserisci un dettaglio per il piano Editoriale</p>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"\"></label>' +
                '<input id=\"titleEvent\" class=\"form-control\"' +
                'placeholder=\"Inserisci il titolo\" name=\"titleEvent\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"startEventDate\">Inserisci la Data di Inizio del Dettaglio </label>' +
                '<input  type =\'datetime\' id=\"startEventDate\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data di Inizio del Dettaglio\" name=\"startEventDate\" value=\"' + start + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"endEventDate\">Inserisci la Data della Fine del Dettaglio </label>' +
                '<input  type =\'datetime\' id=\"EndEventDate\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data della Fine del Dettaglio  \" name=\"endEventDate\" value=\"' + end + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"argument\">Inserisci l\'argomento</label>' +
                '<input id=\"argument\" class=\"form-control\"' +
                'placeholder=\"Inserisci l\'argomento \" name=\"argument\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"description\">Inserisci la Descrizione</label>' +
                '<input id=\"description\" class=\"form-control\"' +
                'placeholder=\"Inserisci la descrizione \" name=\"description\" ">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"photoUrl\">Inserisci il link  immagine </label>' +
                '<input id=\"photoUrl\" class=\"form-control\"' +
                'placeholder=\"Inserisci il link immagine \" name=\"photoUrl\" ">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"note\">Inserisci le note </label>' +
                '<input id=\"note\" class=\"form-control\"' +
                'placeholder=\"Inserisci le note \" name=\"note\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-12">' +
                '<div class="form-group form-group-default selectize-enabled">' +
                '<label for="status">Seleziona lo Stato</label>' +
                '<select id="status" name="status" required="required"' +
                'class="full-width selectpicker"' +
                'placeholder="Seleziona lo stato"' +
                'data-init-plugin="selectize">' +
                '<option value="new">Seleziona lo stato</option>' +
                '<option value="Draft">Bozza</option>' +
                '<option value="Approved">Approvata</option>' +
                '<option value="Rejected">Rifiutata</option>' +
                '<option value="Published">Pubblicata</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"socialPlanId\">Seleziona il media da Associare </label><select id=\"socialPlanId\"  required=\"required\" name=\"socialPlanId\" class=\"full-width selectpicker\" placeholder=\"Selezione il media da associare\"' +
                'data-init-plugin=\"selectize\"></select>' +
                ' </div>' +
                '</div>' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="okSend">Invio</label>' +
                '<div><p>Premere ok per  inserire il dettaglio</p></div>' +
                '</div>' +
                '<input type="hidden" id="editorialPlanId" name="editorialPlanId" value=\"' + id + '\"/>'
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


            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    title: $('#titleEvent').val(),
                    start: start,
                    end: end,
                    argument: $('#argument').val(),
                    description: $('#description').val(),
                    note: $('#note').val(),
                    photoUrl: $('#photoUrl').val(),
                    status: $('#status').val(),
                    socialId: $('#socialPlanId').val(),
                    editorialPlanId: $('#editorialPlanId').val()
                };
                $.ajax({
                    type: 'POST',
                    url: '/blueseal/xhr/EditorialPlanDetailAddAjaxController',
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


        },
        editable: true,
        eventClick: function (event) {

            var title = event.title;
            var editorialPlanDetailId = event.id;
            var argument = event.argument;
            var description = event.description;
            var photoUrl = event.photoUrl;
            var status = event.status;
            var selectedDraft = "";
            var selectedApproved = "";
            var selectedRejected = "";
            var selectedPublished = "";
            let url1 = window.location.href;
            let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);

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
            var note = event.note;
            var socialId = event.socialId;
            var socialName = event.socialName;

            var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
            let bsModal = new $.bsModal('Invio', {
                body: '<p>Modifica un dettaglio per il piano Editoriale</p>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"\"></label>' +
                '<input id=\"titleEvent\" class=\"form-control\"' +
                'placeholder=\"Inserisci il titolo\" name=\"titleEvent\" value=\"' + title + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"startEventDate\">Inserisci la Data di Inizio del Dettaglio </label>' +
                '<input  type =\'datetime\' id=\"startEventDate\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data di Inizio del Dettaglio\" name=\"startEventDate\" value=\"' + start + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"endEventDate\">Inserisci la Data della Fine del Dettaglio </label>' +
                '<input  type =\'datetime\' id=\"EndEventDate\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data della Fine del Dettaglio  \" name=\"endEventDate\" value=\"' + end + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"argument\">Inserisci l\'argomento</label>' +
                '<input id=\"argument\" class=\"form-control\"' +
                'placeholder=\"Inserisci l\'argomento \" name=\"argument\"value=\"' + argument + '\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"description\">Inserisci la Descrizione</label>' +
                '<input id=\"description\" class=\"form-control\"' +
                'placeholder=\"Inserisci la descrizione \" name=\"description\" value=\"' + description + '\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"photoUrl\">Inserisci il link  immagine </label>' +
                '<input id=\"photoUrl\" class=\"form-control\"' +
                'placeholder=\"Inserisci il link immagine \" name=\"photoUrl\" value=\"' + photoUrl + '\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"note\">Inserisci le note </label>' +
                '<input id=\"note\" class=\"form-control\"' +
                'placeholder=\"Inserisci le note \" name=\"note\" value=\"' + note + '\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-12">' +
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
                '</div>' +
                '<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"socialPlanId\">Seleziona il media da Associare </label><select id=\"socialPlanId\"  required=\"required\"   name=\"socialPlanId\" class=\"full-width selectpicker\" placeholder=\"Seleziona il media da Associare\"' +
                'data-init-plugin=\"selectize\">' +
                '<option value="' + socialId + '">' + socialName + '</option>' +
                '</select>' +
                ' </div>' +
                '</div>' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="okSend">Modifica</label>' +
                '<div><p>Premere ok per  inserire il dettaglio</p></div>' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="deleteDetail">Cancellazione</label>' +
                '<div><p>Cancella il Dettaglio</p></div>' +
                '<input type="button" class="btn-success" id="deleteDetail" name="deleteDetail" value="Cancella il Dettaglio del Piano"'+
                '</div>' +
                '<input type="hidden" id="editorialPlanId" name="editorialPlanId" value=\"' + editorialPlanId + '\"/>' +
                '<input type="hidden" id="editorialPlanDetailId" name="editorialPlanDetailId" value=\"' + editorialPlanDetailId + '\"/>'
            });
            $("#deleteDetail").click(function() {
                if (confirm("Sei Sicuro di Cancellare il Dettaglio del Piano Editoriale")) {
                    var id = event.id;
                    $.ajax({
                        url: "/blueseal/xhr/EditorialPlanDetailEditAjaxController",
                        type: "put",
                        data: {id: id},
                        success: function (res) {

                            alert(res);
                            calendar.fullCalendar('refetchEvents');

                        }

                    })
                    bsModal.hide();
                    window.location.reload();
                }
            });
            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'EditorialPlanSocial',
                    selection: {id: socialId}

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


            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    title: $('#titleEvent').val(),
                    start: start,
                    end: end,
                    argument: $('#argument').val(),
                    description: $('#description').val(),
                    note: $('#note').val(),
                    photoUrl: $('#photoUrl').val(),
                    status: $('#status').val(),
                    socialId: $('#socialPlanId').val(),
                    editorialPlanId: $('#editorialPlanId').val(),
                    editorialPlanDetailId: $('#editorialPlanDetailId').val()

                };
                $.ajax({
                    type: 'POST',
                    url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
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


        },

        eventDrop: function (event) {
            start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
            end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

            var title = event.title;
            var editorialPlanDetailId = event.id;
            var argument = event.argument;
            var description = event.description;
            var photoUrl = event.photoUrl;
            var status = event.status;
            var note = event.note;
            var socialId = event.socialId;

            let url1 = window.location.href;
            let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);
            $.ajax({
                url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
                type: 'POST',
                data: {
                    title: title,
                    start: start,
                    end: end,
                    note: note,
                    editorialPlanId: editorialPlanId,
                    editorialPlanDetailId: editorialPlanDetailId,
                    argument: argument,
                    description:
                    description,
                    photoUrl: photoUrl,
                    status: status,
                    socialId: socialId
                },
                success: function () {
                    calendar.fullCalendar('refetchEvents');
                    alert("Dettaglio Piano Editoriale Aggiornato");

                }
            });
        },
        eventResize: function (event) {
            start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
            end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

            var title = event.title;
            var editorialPlanDetailId = event.id;
            var argument = event.argument;
            var description = event.description;
            var photoUrl = event.photoUrl;
            var status = event.status;
            var note = event.note;
            var socialId = event.socialId;

            let url1 = window.location.href;
            let editorialPlanId = url1.substring(url1.lastIndexOf('/') + 1);
            $.ajax({
                url: '/blueseal/xhr/EditorialPlanDetailEditAjaxController',
                type: 'POST',
                data: {
                    title: title,
                    start: start,
                    end: end,
                    note: note,
                    editorialPlanId: editorialPlanId,
                    editorialPlanDetailId: editorialPlanDetailId,
                    argument: argument,
                    description:
                    description,
                    photoUrl: photoUrl,
                    status: status,
                    socialId: socialId
                },
                success: function () {
                    calendar.fullCalendar('refetchEvents');
                    alert("Dettaglio Piano Editoriale Aggiornato");

                }
            });
        },

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
        },

    });


})
(jQuery);

