(function ($) {

    var eventColor;
    var obj = null;
    var typeView = 1;
    var facebookCampaignId = '';
    var groupInsertionId = '';
    $(document).ready(function () {
        $(this).trigger('bs.load.photo');
        createcalendar(obj, 1);
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/EditorialPlanSocialFilterAjaxController',
        }).done(function (res) {
            let ret = JSON.parse(res);


            $.each(ret.social, function (k, v,) {
                $('#filterMedia').append('<div style ="background-color:' + ret.socialcolor[k] + ';"><i class="' + ret.socialicon[k] + '"><input type="checkbox" name="' + v + '" value="' + k + '" /> ' + v + '</i></div>');
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
            socialId: checkedSocial,
            id: id
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/EditorialPlanDetailListFullAjaxController',
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
            url: '/blueseal/xhr/EditorialPlanDetailListFullAjaxController',
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
                    var bgRender = "#ffffff";
                    var bgTitle = "#ffffff";


                    eventColor = "";
                    bgTitle = '<div style="background-color:' + event.color + ';color:black;">';
                    switch (event.status) {
                        case "Bozza":
                            bgRender = '<div style="background-color:#f8bb00 ;color:black;">';

                            break;
                        case "Approvata":
                            bgRender = '<div style="background-color:#fa6801 ;color:black;"">';

                            break;
                        case "Rifiutata":
                            bgRender = '<div style="background-color:#f22823 ;color:black;"">';

                            break;
                        case "Pubblicata":
                            bgRender = '<div style="background-color:#3e8f3e ;color:black;">';

                            break;

                    }


                    var linkimg = "";
                    var link = event.photoUrl.split(",");
                    link.forEach(function (element) {
                        linkimg = linkimg + ' <img width="80px" src="' + element + '">';

                    });
                    if (typeView == 1) {
                        element.find('.fc-title').append(bgTitle +
                            '<b>' + event.argumentName +
                            ' | ' + event.titleEditorialPlan +
                            ' | ' + event.socialName +
                            ' | ' + event.status + '</b></div>' + bgRender +
                            '<br><b>' + event.note + '</b><br>' + linkimg +
                            '</div>');
                    } else {
                        element.find('.fc-title').append(bgTitle +
                            '<b>' + event.argumentName +
                            ' | ' + event.titleEditorialPlan +
                            ' | ' + event.socialName +
                            ' | ' + event.status + '</b><br>' + bgRender +
                            '<br></div>');
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

                    var start = $.fullCalendar.formatDate(start, "DD-MM-YYYY HH:mm:ss");
                    var end = $.fullCalendar.formatDate(end, "DD-MM-YYYY HH:mm:ss");
                    let bsModal1 = new $.bsModal('Invio', {
                        body: '<p>Inserisci un Evento per il Piano Editoriale</p>' +
                            `<div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="socialPlanId">Seleziona il media da Associare </label>
                                            <select id="socialPlanId"
                                                    required="required"
                                                    name="socialPlanId"
                                                    class="full-width selectpicker"
                                                    placeholder="Selezione il media da associare"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="status">Seleziona lo Stato</label>
                                            <select id="status" name="status" required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona lo stato"
                                                    data-init-plugin="selectize">
                                                <option value="new">Stato</option>
                                                <option value="Draft">Bozza</option>
                                                <option value="Approved">Approvata</option>
                                                <option value="Rejected">Rifiutata</option>
                                                <option value="Published">Pubblicata</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="startEventDate">Data Inizio Evento</label>
                                            <input type="datetime-local" id="startEventDate" class="form-control"
                                                   placeholder="Inserisci la Data di Inizio del Dettaglio"
                                                   name="startEventDate" value=""
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="endEventDate">Data Fine Evento </label>
                                            <input type="datetime-local" id="endEventDate" class="form-control"
                                                   placeholder="Inserisci la Data della Fine del Dettaglio "
                                                   name="endEventDate" value=""
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
                                                <option value="notNotify">Non Inviare la Notifica</option>
                                                <option value="yesNotify">Invia la Notifica</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="editorialPlanId">Seleziona Piano Editoriale</label>
                                            <select id="editorialPlanId"
                                                    name="editorialPlanId" class="full-width selectpicker"
                                                    required="required"
                                                    placeholder="Selezione il piano editoriale da utilizzare"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="foisonId">Seleziona Operatore</label>
                                            <select id="foisonId"
                                                    name="foisonId" class="full-width selectpicker"
                                                    placeholder="Selezione Operatore"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="editorialPlanArgumentId">Tipo Di Creatività</label>
                                            <select id="editorialPlanArgumentId"
                                                    name="editorialPlanArgumentId" class="full-width selectpicker"
                                                    required="required"
                                                    placeholder="Selezione argomento da utilizzare"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleEditorialPlanArgument">Visibile</label>
                                            <input type="checkbox" id="isVisibleEditorialPlanArgument"
                                                   class="form-control"
                                                   placeholder="Visible" checked="true"
                                                   name="isVisibleEditorialPlanArgument" ">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div id="divSelecterCampaign" class="hide">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="selecterCampaign">Seleziona Operazione su </label>
                                                <select id="selecterCampaign"
                                                        name="selecterCampaign" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione operazioni su campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <option value="0">Crea Nuova</option>
                                                    <option value="1">Seleziona Esistente</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="divCampaign" class="hide">

                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="titleEvent">Titolo Azione Evento</label>
                                            <input id="titleEvent" class="form-control"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isEventVisible">Visibile</label>
                                            <input type="checkbox" id="isEventVisible" class="form-control"
                                                   placeholder="Visible" checked="true" name="isEventVisible"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="description">Descrizione Evento</label>
                                            <textarea id="description" cols="60" rows="10"
                                                      placeholder="Inserisci la descrizione dell'evento"
                                                      name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleDescription">Visibile</label>
                                            <input type="checkbox" id="isVisibleDescription" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisibleDescription">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="bodyEvent">Testo Evento</label>
                                            <textarea id="bodyEvent" cols="100" rows="10" name="bodyEvent"
                                                      placeholder="Inserisci il testo dell'evento "></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="linkDestination">Link Destinazione</label>
                                            <textarea id="linkDestination" cols="100" rows="1"
                                                      placeholder="Inserisci  i link di destinazione"
                                                      name="linkDestination"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleBodyEvent">Visibile</label>
                                            <input type="checkbox" id="isVisibleBodyEvent" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisibleBodyEvent"/>
                                        </div>
                                    </div>
                                </div>
                                <div id="divPostUploadImage" class="hide">` +
                            bodyContent + `</div>` +
                            `<div id="divPostImage" class="hide">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postImageTitle">Creatività:Titolo Post</label>
                                                <textarea id="postImageTitle" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine "
                                                          name="postImageTitle"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postImageDescription">Creatività:Descrizione Immagine</label>
                                                <textarea id="postImageDescription" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 1"
                                                          name="postImageDescription"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postImageUrl">Creatività:link Immagine</label>
                                                <textarea id="postImageUrl" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 1"
                                                          name="postImageUrl"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="divPostCarousel" class="hide">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle1">Creatività:Titolo Post Immagine1</label>
                                                <textarea id="imageTitle1" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 1"
                                                          name="imageTitle1"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage1">Creatività:Descrizione Immagine1</label>
                                                <textarea id="descriptionImage1" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 1"
                                                          name="descriptionImage1"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl1">Creatività:link Immagine1</label>
                                                <textarea id="imageUrl1" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 1"
                                                          name="imageUrl1"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle2">Creatività:Titolo Post Immagine2</label>
                                                <textarea id="imageTitle2" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 1"
                                                          name="imageTitle2"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage2">Creatività:Descrizione Immagine2</label>
                                                <textarea id="descriptionImage2" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 2"
                                                          name="descriptionImage1"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl2">Creatività:link Immagine2</label>
                                                <textarea id="imageUrl2" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 2"
                                                          name="imageUrl2"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle3">Creatività:Titolo Post Immagine3</label>
                                                <textarea id="imageTitle3" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 3"
                                                          name="imageTitle3"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage3">Creatività:Descrizione Immagine3</label>
                                                <textarea id="descriptionImage3" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 3"
                                                          name="descriptionImage3"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl3">Creatività:link Immagine3</label>
                                                <textarea id="imageUrl3" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 3"
                                                          name="imageUrl3"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle4">Creatività:Titolo Post Immagine4</label>
                                                <textarea id="imageTitle4" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 4"
                                                          name="imageTitle4"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage4">Creatività:Descrizione Immagine4</label>
                                                <textarea id="descriptionImage4" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 4"
                                                          name="descriptionImage4"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl4">Creatività:link Immagine4</label>
                                                <textarea id="imageUrl4" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 4"
                                                          name="imageUrl4"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle5">Creatività:Titolo Post  Immagine5</label>
                                                <textarea id="imageTitle5" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 5"
                                                          name="imageTitle5"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage5">Creatività:Descrizione Immagine5</label>
                                                <textarea id="descriptionImage5" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 5"
                                                          name="descriptionImage5"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl5">Creatività:link Immagine5</label>
                                                <textarea id="imageUrl5" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 5"
                                                          name="imageUrl5"></textarea>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle6">Creatività:Titolo Post Immagine6</label>
                                                <textarea id="imageTitle6" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 6"
                                                          name="imageTitle6"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage6">Creatività:Descrizione Immagine6</label>
                                                <textarea id="descriptionImage6" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 6"
                                                          name="descriptionImage6"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl6">Creatività:link Immagine6</label>
                                                <textarea id="imageUrl6" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 6"
                                                          name="imageUrl6"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle7">Creatività:Titolo Post Immagine7</label>
                                                <textarea id="imageTitle7" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 7"
                                                          name="imageTitle7"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage7">Creatività:Descrizione Immagine7</label>
                                                <textarea id="descriptionImage7" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 7"
                                                          name="descriptionImage7"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl7">Creatività:link Immagine7</label>
                                                <textarea id="imageUrl7" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 7"
                                                          name="imageUrl7"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle8">Creatività:Titolo Post Immagine8</label>
                                                <textarea id="imageTitle8" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 8"
                                                          name="imageTitle8"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage8">Creatività:Descrizione Immagine8</label>
                                                <textarea id="descriptionImage8" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 8"
                                                          name="descriptionImage8"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl8">Creatività:link Immagine8</label>
                                                <textarea id="imageUrl8" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 8"
                                                          name="imageUrl8"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle9">Creatività:Titolo Post Immagine9</label>
                                                <textarea id="imageTitle9" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 9"
                                                          name="imageTitle9"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage9">Creatività:Descrizione Immagine9</label>
                                                <textarea id="descriptionImage9" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 9"
                                                          name="descriptionImage9"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl9">Creatività:link Immagine6</label>
                                                <textarea id="imageUrl9" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 9"
                                                          name="imageUrl9"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageTitle10">Creatività:Titolo Post Immagine10</label>
                                                <textarea id="imageTitle10" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine 10"
                                                          name="imageTitle6"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="descriptionImage10">Creatività:Descrizione Immagine10</label>
                                                <textarea id="descriptionImage10" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 10"
                                                          name="descriptionImage10"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="imageUrl10">Creatività:link Immagine10</label>
                                                <textarea id="imageUrl10" class="form-control"
                                                          placeholder="Inserisci il link per l'immagine 10"
                                                          name="imageUrl10"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="postVideo" class="hide">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postVideoTitle">Creatività:Titolo Video</label>
                                                <textarea id="postVideoTitle" class="form-control"
                                                          placeholder="Inserisci il titolo per l'immagine "
                                                          name="postVideoTitle"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postDescriptionVideo">Creatività:Descrizione Video</label>
                                                <textarea id="postDescriptionVideo" class="form-control"
                                                          placeholder="Inserisci la descrizione per l'immagine 1"
                                                          name="postDescriptionVideo"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="video1">Creatività:link Video</label>
                                                <textarea id="video1" class="form-control"
                                                          placeholder="Inserisci il link per il video " name="video"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="postVideoCallToAction">Creatività:Seleziona la Call To Action</label>
                                                <select id="postVideoCallToAction"
                                                        name="postVideoCallToAction" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione il piano editoriale da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <option value="OPEN_LINK">APRI LINK</option>
                                                    <option value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option value="SHOP_NOW">SHOP NOW</option>
                                                    <option value="CALL">CALL_ME</option>
                                                    <option value="APPLY_NOW">APPLY NOW</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="note">Note Evento</label>
                                            <textarea id="note" cols="100" rows="10" name="note"
                                                      placeholder="Inserisci le note"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleNote">Visibile</label>
                                            <input type="checkbox" id="isVisibleNote" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisibleNote"/>
                                        </div>
                                    </div>
                                </div>` +

                            '<div class="form-group form-group-default required">' +
                            '<label for="okSend">Invio</label>' +
                            '<div><p>Premere ok per  inserire il dettaglio</p></div>' +
                            '</div>' +
                            '<input type="hidden" id="editorialPlanId" name="editorialPlanId" value=\"' + id + '\"/>'
                    });
                    var photoUrl = [];

                    bsModal1.addClass('modal-wide');
                    bsModal1.addClass('modal-high');

                    $('#isEventVisible').prop('checked', true);
                    $('#isVisibleEditorialPlanArgument').prop('checked', true);
                    $('#isVisibleDescription').prop('checked', true);
                    $('#isVisibleNote').prop('checked', true);
                    $('#isVisibleBodyEvent').prop('checked', true);
                    $('#isVisiblePhotoUrl').prop('checked', true);
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
                        okButton.attr("disabled", "disabled");
                        let urlimage = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
                        let filename = file.name;
                        let image = urlimage + filename;
                        photoUrl.push(image);
                    });
                    dropzone.on('queuecomplete', function () {
                        okButton.removeAttr("disabled");
                        $(document).trigger('bs.load.photo');
                    });
                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlan'
                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#editorialPlanId');
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
                                        '<span class="caption">' + escape(item.startDate) + ' | ' + escape(item.endDate) + '</span>' +
                                        '</div>'
                                },
                                option: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.name) + '</span> - ' +
                                        '<span class="caption">' + escape(item.startDate) + ' | ' + escape(item.endDate) + '</span>' +
                                        '</div>'
                                }
                            },
                            onInitialize: function () {
                                var selectize = this;
                                selectize.setValue($('#editorialPlanSelectId').val());
                            }
                        });
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
                            options: res2
                        });
                    });

                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/SelectFoisonAjaxController',
                        data: {
                            table: 'Foison'
                        },
                        dataType: 'json'
                    }).done(function (res3) {
                        var select = $('#foisonId');
                        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                        select.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res3,
                            render: {
                                item: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.name) + '</span> - ' +
                                        '<span class="caption">' + escape(item.rank) + '</span>' +
                                        '</div>'
                                },
                                option: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.name) + '</span> - ' +
                                        '<span class="caption">' + escape(item.rank) + '</span>' +
                                        '</div>'
                                }
                            },
                            onInitialize: function () {
                                var selectize = this;
                                selectize.setValue($('#foisonSelectId').val());
                            }
                        });
                    });
                    var editorialPlanSocialSelected = '';
                    $('#socialPlanId').on('change', function () {
                        editorialPlanSocialSelected = $(this).val();
                        $.ajax({
                            method: 'GET',
                            url: '/blueseal/xhr/GetTableContent',
                            data: {
                                table: 'EditorialPlanArgument',
                                condition: {editorialPlanSocialId: editorialPlanSocialSelected}
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

                    });
                    $('#selecterCampaign').change(function () {
                        var selecterTypeOperation = $(this).val();
                        if (selecterTypeOperation == '0') {
                            var bodyForm = `               <div class="row">
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignName">Nome Della Campagna</label>
                                                <input id="campaignName" class="form-control"
                                                       placeholder="Inserisci il nome Campagna" name="campaignName"
                                                       required="required">
                                            </div>
                                        </div>
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="groupAdsName">Nome del Gruppo inserzioni</label>
                                                <input id="groupAdsName" class="form-control"
                                                       placeholder="Inserisci il Nome del gruppo Inserzioni" name="groupAdsName"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="buying_type">Tipo di Acquisto</label>
                                                <select id="buying_type"
                                                        name="buying_type" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                        <option value="AUCTION">Asta</option>
                                                        <option value="RESERVED">Copertura e Frequenza</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="objective">Obiettivo della Campagna</label>
                                                <select id="objective"
                                                        name="objective" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                       <option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                       <option value="REACH">Copertura</option>
                                                       <option value="LOCAL_AWARENESS">Traffico</option>
                                                       <option value="APP_INSTALLS">installazioni dell\'App</option>
                                                       <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                       <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                       <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                       <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                       <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                       <option value="MESSAGES">Messaggi</option>
                                                       <option value="CONVERSIONS">Conversioni</option>
                                                       <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>
                                                       <option value="STORE_VISITS">Traffico nel punto Vendita</option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lifetime_budget">Importo Budget Totale</label>
                                                <input id="lifetime_budget" class="form-control"
                                                       placeholder="Inserisci il Budget" name="lifetime_budget"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                           <div class="form-group form-group-default selectize-enabled">
                                        <button class="btn btn-primary" id="addCampaign" name="addCampaign" onclick="addCampaign()" <span
                                                class="fa fa-save">Salva Campagna</span></button>
                                            <input type="hidden" id="facebookCampaignId" name="facebookCampaignId" value=""/> 
                                            <input type="hidden" id="isNewAdset" name="isNewAdset" value="1"/>
                                            </div>
                                        </div>
                                    </div>`;
                            $('#divCampaign').removeClass('hide');
                            $('#divCampaign').empty();
                            $('#divCampaign').append(bodyForm);


                        } else {

                            var bodyForm = `<div class="row">
           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                <label for="campaignName">Seleziona Campagna</label>
                    <select id="campaignName"
                           name="campaignName" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione campagna da utilizzare"
                           data-init-plugin="selectize">
                   </select>
               </div>
           </div>
            <div class="col-md-2">
            <div id="divgroupAdsName">
            
            </div>
            </div>
           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <label for="buying_type">Tipo di Acquisto</label>
                   <select id="buying_type"
                           name="buying_type" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione campagna da utilizzare"
                           data-init-plugin="selectize">
                       <option value="AUCTION">Asta</option>
                       <option value="RESERVED">Copertura e Frequenza</option>
                   </select>
               </div>
           </div>
           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <label for="objective">Obiettivo della Campagna</label>
                   <select id="objective"
                           name="objective" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione campagna da utilizzare"
                           data-init-plugin="selectize">
                       <option value="BRAND_AWARENESS">Notorietà del Brand</option>
                       <option value="REACH">Copertura</option>
                       <option value="LOCAL_AWARENESS">Traffico</option>
                       <option value="APP_INSTALLS">installazioni dell\'App</option>
                       <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                       <option value="LEAD_GENERATION">Generazione di Contatti</option>
                       <option value="POST_ENGAGEMENT">interazione con i post</option>
                       <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                       <option value="EVENT_RESPONSES">Risposte a un evento</option>
                       <option value="MESSAGES">Messaggi</option>
                       <option value="CONVERSIONS">Conversioni</option>
                       <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>
                       <option value="STORE_VISITS">Traffico nel punto Vendita</option>
                   </select>
               </div>
           </div>

           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <label for="lifetime_budget">Importo Budget Totale</label>
                   <input id="lifetime_budget" class="form-control"
                          placeholder="Inserisci il Budget" name="lifetime_budget"
                          required="required">
               </div>
           </div>
           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <button class="btn btn-primary" id="updateCampaign" onclick="updateCampaign()"  type="button"><span
                       class="fa fa-save">Aggiorna Campagna</span></button>
                   <input type="hidden" id="facebookCampaignId" name="facebookCampaignId" value=""/>
                    <input type="hidden" id="isNewAdset" name="isNewAdset" value="2"/>
               </div>
           </div>
       </div>`;
                            $('#divCampaign').removeClass('hide');
                            $('#divCampaign').empty();
                            $('#divCampaign').append(bodyForm);

                            $.ajax({
                                url: '/blueseal/xhr/SelectFacebookCampaignAjaxController',
                                method: 'get',
                                data: {
                                    editorialPlanId: $('#editorialPlanId').val()
                                },
                                dataType: 'json'
                            }).done(function (res) {
                                console.log(res);
                                let campaignName = $('#campaignName');
                                //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
                                campaignName.selectize({
                                    valueField: 'idCampaign',
                                    labelField: 'nameCampaign',
                                    searchField: ['nameCampaign'],
                                    options: res,
                                    render: {
                                        item: function (item, escape) {
                                            return '<div>' +
                                                '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                                                '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                                                '</div>'
                                        },
                                        option: function (item, escape) {
                                            return '<div>' +
                                                '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                                                '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                                                '</div>'
                                        }
                                    }
                                });
                            });
                            $('#campaignName').change(function () {
                                var bodyGroupAdsName = `<div class="form-group form-group-default selectize-enabled">
                <label for="groupAdsName">Seleziona il Gruppo Inserzioni</label>
                    <select id="groupAdsName"
                           name="groupAdsName" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione il Gruppo inserzioni"
                           data-init-plugin="selectize">
                   </select>
               </div>`;
                                $('#divgroupAdsName').empty();
                                $('#divgroupAdsName').append(bodyGroupAdsName);
                                $.ajax({
                                    url: '/blueseal/xhr/SelectFacebookAdSetAjaxController',
                                    method: 'get',
                                    data: {
                                        campaignId: $('#campaignName').val(),
                                        editorialPlanId: $('#editorialPlanId').val()
                                    },
                                    dataType: 'json'
                                }).done(function (res) {
                                    console.log(res);
                                    let groupAdsName = $('#groupAdsName');
                                    //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
                                    groupAdsName.selectize({
                                        valueField: 'idAdSet',
                                        labelField: 'nameAdSet',
                                        searchField: ['nameAdSet'],
                                        options: res,
                                        render: {
                                            item: function (item, escape) {
                                                return '<div>' +
                                                    '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                                                    '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                                                    '</div>'
                                            },
                                            option: function (item, escape) {
                                                return '<div>' +
                                                    '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                                                    '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                                                    '</div>'
                                            }
                                        }
                                    });
                                });


                            });

                        }

                    });


                    $('#editorialPlanArgumentId').change(function () {
                        if ($('#socialPlanId').val() == 1) {
                            $('#divSelecterCampaign').removeClass('hide');
                            $('#divSelecterCampaign').addClass('show');
                        } else {
                            $('#divSelecterCampaign').removeClass('show');
                            $('#divSelecterCampaign').addClass('hide');
                        }
                        var isFacebook=$(this).val();
                        switch(isFacebook){
                            case "5":
                                $('#divSelecterCampaign').removeClass('show');
                                $('#divSelecterCampaign').addClass('hide');
                                $('#divCampaign').removeClass('show');
                                $('#divCampaign').addClass('hide');
                                $('#divPostUploadImage').removeClass('hide');
                                $('#divPostUploadImage').addClass('show');
                                $('#divPostImage').removeClass('hide');
                                $('#divPostImage').addClass('show');
                                $('#divPostCarousel').removeClass('show');
                                $('#divPostCarousel').addClass('hide');
                                $('#postVideo').removeClass('show');
                                $('#postVideo').addClass('hide');
                                break;
                            case "8":
                                $('#divPostUploadImage').removeClass('hide');
                                $('#divPostUploadImage').addClass('show');
                                $('#divPostImage').removeClass('hide');
                                $('#divPostImage').addClass('show');
                                $('#divPostCarousel').removeClass('show');
                                $('#divPostCarousel').addClass('hide');
                                $('#postVideo').removeClass('show');
                                $('#postVideo').addClass('hide');
                                break;
                            case "9":
                                $('#divPostUploadImage').removeClass('hide');
                                $('#divPostUploadImage').addClass('show');
                                $('#divPostImage').removeClass('show');
                                $('#divPostImage').addClass('hide');
                                $('#divPostCarousel').removeClass('hide');
                                $('#divPostCarousel').addClass('show');
                                $('#postVideo').removeClass('show');
                                $('#postVideo').addClass('hide');
                                break;
                            case "10":
                                $('#divPostUploadImage').removeClass('show');
                                $('#divPostUploadImage').addClass('hide');
                                $('#divPostImage').removeClass('show');
                                $('#divPostImage').addClass('hide');
                                $('#divPostCarousel').removeClass('show');
                                $('#divPostCarousel').addClass('hide');
                                $('#postVideo').removeClass('hide');
                                $('#postVideo').addClass('show');
                                break;
                        }

                    });


                    bsModal1.showCancelBtn();
                    bsModal1.setOkEvent(function () {
                        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
                        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
                        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
                        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
                        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
                        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
                        start = $('#startEventDate').val();
                        end = $('#endEventDate').val();
                        if ($('#facebookCampaignId').length) {
                            facebookCampaignId = $('#facebookCampaignId').val();
                        } else {
                            facebookCampaignId = 'notExist';
                        }
                        if ($('#campaignName').length) {
                            campaignName = $('#campaignName').val();
                        } else {
                            campaignName = 'notExist';
                        }
                        var groupAdsName = '';
                        if ($('#groupAdsName').length) {
                            groupAdsName = $('#groupAdsName').val();
                        }
                        var isNewAdSet = 0;
                        if ($('#isNewAdset').length) {
                            isNewAdSet = $('#isNewAdset').val();
                        }

                        const data = {
                            title: $('#titleEvent').val(),
                            start: start,
                            end: end,
                            argument: $('#editorialPlanArgumentId').val(),
                            description: $('#description').val(),
                            note: $('#note').val(),
                            isVisibleNote: isVisNote,
                            photoUrl: photoUrl,
                            status: $('#status').val(),
                            socialId: $('#socialPlanId').val(),
                            editorialPlanId: $('#editorialPlanId').val(),
                            notifyEmail: $('#notifyEmail').val(),
                            linkDestination: $('#linkDestination').val(),
                            isEventVisible: isEvVisible,
                            isVisibleEditorialPlanArgument: isVisEdPlanArg,
                            isVisiblePhotoUrl: isVisPhoto,
                            isVisibleDescription: isVisDesc,
                            bodyEvent: $('#bodyEvent').val(),
                            isVisibleBodyEvent: isVisBody,
                            facebookCampaignId: facebookCampaignId,
                            campaignId: campaignName,
                            groupAdsName: groupAdsName,
                            isNewAdSet: isNewAdSet,
                            selecterCampaign: $('#selecterCampaign').val(),
                            lifetime_budget: $('#lifetime_budget').val(),
                            buying_type: $('#buying_type').val(),
                            objective: $('#objective').val(),
                            imageTitle1:$('#imageTitle1').val(),
                            imageTitle2:$('#imageTitle2').val(),
                            imageTitle3:$('#imageTitle3').val(),
                            imageTitle4:$('#imageTitle4').val(),
                            imageTitle5:$('#imageTitle5').val(),
                            imageTitle6:$('#imageTitle6').val(),
                            imageTitle7:$('#imageTitle7').val(),
                            imageTitle8:$('#imageTitle8').val(),
                            imageTitle9:$('#imageTitle9').val(),
                            imageTitle10:$('#imageTitle10').val(),
                            imageUrl1:$('#imageUrl1').val(),
                            imageUrl2:$('#imageUrl2').val(),
                            imageUrl3:$('#imageUrl3').val(),
                            imageUrl4:$('#imageUrl4').val(),
                            imageUrl5:$('#imageUrl5').val(),
                            imageUrl6:$('#imageUrl6').val(),
                            imageUrl7:$('#imageUrl7').val(),
                            imageUrl8:$('#imageUrl8').val(),
                            imageUrl9:$('#imageUrl9').val(),
                            imageUrl10:$('#imageUrl10').val(),
                            descriptionImage1:$('#descriptionImage1').val(),
                            descriptionImage2:$('#descriptionImage2').val(),
                            descriptionImage3:$('#descriptionImage3').val(),
                            descriptionImage4:$('#descriptionImage4').val(),
                            descriptionImage5:$('#descriptionImage5').val(),
                            descriptionImage6:$('#descriptionImage6').val(),
                            descriptionImage7:$('#descriptionImage7').val(),
                            descriptionImage8:$('#descriptionImage8').val(),
                            descriptionImage9:$('#descriptionImage9').val(),
                            descriptionImage10:$('#descriptionImage10').val(),
                            postImageTitle:$('#postImageTitle').val(),
                            postImageDescription:$('#postImageDescription').val(),
                            postVideoTitle:$('#postVideoTitle').val(),
                            postDescriptionVideo:$('#postDescriptionVideo').val(),
                            postVideoCallToAction: $('#postVideoCallToAction').val(),
                            foisonId:$('#foisonId').val(),
                            video1:$('#video1').val(),
                            type:'formCalendar',


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
                    var linkDestination = event.linkDestination;
                    var photoUrl = event.photoUrl;
                    var status = event.status;
                    facebookCampaignId = event.facebookCampaignId;
                    groupInsertionId = event.groupInsertionId;
                    var buying_type = event.buying_type;
                    var lifetime_budget = event.lifetime_budget;
                    var objective = event.objective;
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
                    var buying_typeSelected = '';
                    if (buying_type == 'AUCTION') {
                        buying_typeSelected = `<option selected="selected" value="AUCTION">Asta</option>
                        <option value="RESERVED">Copertura e Frequenza</option>`;
                    } else {
                        buying_typeSelected = `<option  value="AUCTION">Asta</option>
                        <option  selected="selected" value="RESERVED">Copertura e Frequenza</option>`;
                    }


                    var checkedObjective = '';
                    switch (objective) {
                        case 'BRAND_AWARENESS':
                            checkedObjective = `<option selected="selected" value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option value="REACH">Copertura</option>
                            <option value="LOCAL_AWARENESS">Traffico</option>
                            <option value="APP_INSTALLS">installazioni dell\'App</option>
                            <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del
                                catalogo
                            </option>`;
                            break;
                        case 'REACH':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option selected="selected" value="REACH">Copertura</option>
                            <option value="LOCAL_AWARENESS">Traffico</option>
                            <option value="APP_INSTALLS">installazioni dell\'App</option>
                            <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'LOCAL_AWARENESS':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option selected="selected" value="LOCAL_AWARENESS">Traffico</option>
                            <option value="APP_INSTALLS">installazioni dell\'App</option>
                            <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'APP_INSTALLS':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option selected="selected" value="APP_INSTALLS">installazioni dell\'App</option>
                            <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'VIDEO_VIEWS':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option selected="selected" value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'LEAD_GENERATION':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option selected="selected" value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'POST_ENGAGEMENT':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option selected="selected" value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'PAGE_LIKES':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option  value="POST_ENGAGEMENT">interazione con i post</option>
                            <option selected="selected" value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'EVENT_RESPONSES'  :
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option  value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option  selected="selected" value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'MESSAGES':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option  value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option selected="selected" value="MESSAGES">Messaggi</option>
                            <option value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'CONVERSIONS':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option  value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option  value="MESSAGES">Messaggi</option>
                            <option selected="selected" value="CONVERSIONS">Conversioni</option>
                            <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;
                        case 'PRODUCT_CATALOG_SALES':
                            checkedObjective = `<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                        <option  value="REACH">Copertura</option>
                            <option  value="LOCAL_AWARENESS">Traffico</option>
                            <option  value="APP_INSTALLS">installazioni dell\'App</option>
                            <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                            <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                            <option  value="POST_ENGAGEMENT">interazione con i post</option>
                            <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                            <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                            <option  value="MESSAGES">Messaggi</option>
                            <option  value="CONVERSIONS">Conversioni</option>
                            <option selected="selected" value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>`;
                            break;

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
                    var linkimg = "";
                    var link = event.photoUrl.split(",");

                    link.forEach(function (element) {
                        linkimg = linkimg + '<br/><img width="150px" src="' + element + '">';
                    });


                    var photogroup = "";
                    let bodyContent =
                        '<div class="col-md-3">' +
                        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="photoUrl" name="photoUrl" action="POST">' +
                        '<div class=\"form-group form-group-default selectize-enabled\">' +
                        '<label for=\"file\">Immagine Evento' + linkimg + '</label>' +
                        '<div class=\"fallback\">' +
                        //'<label for=\"file\">Immagine Evento'+linkimg+'</label>' +
                        '<input name="file1" type="file" multiple />' +
                        '</div>' +
                        '</div>' +
                        '</div>' +

                        '</form>';
                    $('#photoUrl').change(function () {
                        photogroup = $('#photoUrl').val();
                    });
                    body.html(bodyContent);


                    var start = $.fullCalendar.formatDate(event.start, "DD-MM-YYYY HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "DD-MM-YYYY HH:mm:ss");
                    let bsModal2 = new $.bsModal('Invio', {
                        body: '<p>Modifica l\'evento per il Piano Editoriale</p>' +
                            '<div class=\"row\">' +
                            '<div class="col-md-3">' +
                            '<div class="form-group form-group-default selectize-enabled">' +
                            '<label for="editorialPlanId">Seleziona Piano Editoriale</label>' +
                            '<select id="editorialPlanId"' +
                            'name="editorialPlanId" className="full-width selectpicker"' +
                            'required="required"' +
                            'placeholder="Selezione il piano editoriale da utilizzare"' +
                            'data-init-plugin="selectize"></select>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '<div class=\"row\">' +
                            `<div class="col-md-2">
                              <div class="form-group form-group-default selectize-enabled">
                              <label for="campaignName">Seleziona Campagna</label>
                              <select id="campaignName"
                               name="campaignName" class="full-width selectpicker"
                                required="required"
                                placeholder="Selezione campagna da utilizzare"
                                data-init-plugin="selectize">
                               </select>
                               </div>
                               </div>
                               <div class="col-md-2">
                               <div class="form-group form-group-default selectize-enabled">
                               <label for="groupAdsName">Seleziona il Gruppo Inserzioni</label>
                               <select id="groupAdsName"
                                name="groupAdsName" class="full-width selectpicker"
                                required="required"
                                placeholder="Selezione il Gruppo inserzioni"
                                data-init-plugin="selectize">
                                </select>
             
                               </div>
                               </div>
                        <div class="col-md-2">
                         <div class="form-group form-group-default selectize-enabled">
                         <label for="buying_type">Tipo di Acquisto</label>
                   <select id="buying_type"
                           name="buying_type" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione campagna da utilizzare"
                           data-init-plugin="selectize">` + buying_typeSelected + `
                   </select>
               </div>
           </div>
           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <label for="objective">Obiettivo della Campagna</label>
                   <select id="objective"
                           name="objective" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione campagna da utilizzare"
                           data-init-plugin="selectize">` + checkedObjective + `
                       
                   </select>
               </div>
           </div>

           <div class="col-md-2">
               <div class="form-group form-group-default selectize-enabled">
                   <label for="lifetime_budget">Importo Budget Totale</label>
                   <input id="lifetime_budget" class="form-control"
                          placeholder="Inserisci il Budget" name="lifetime_budget"
                          required="required" value="` + lifetime_budget + `">
               </div>
           </div>` +
                            '</div>' +
                            '<div class=\"row\">' +
                            '<div class="col-md-3">' +
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
                            '</div>' + bodyContent +
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
                            '<div class=\"col-md-3\">' +
                            '<div class=\"form-group form-group-default selectize-enabled\">' +
                            '<label for=\"linkDestination\">Link Destinazione</label>' +
                            '<input id=\"linkDestination\" class=\"form-control\"' +
                            'placeholder=\"Modifica il link \" name=\"linkDestination\" value=\"' + linkDestination + '\">' +
                            '</div>' +
                            '</div>' +
                            '<div class=\"col-md-6\">' +
                            '<div class=\"form-group form-group-default selectize-enabled\">' +
                            '<label for=\"bodyEvent\">Testo Evento</label>' +
                            '<textarea id="bodyEvent" cols="50" rows="10" name="bodyEvent">' + bodyEvent + '</textarea>' +
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
                            '<textarea id="note" cols="50" rows="10" name="note" placeholder="Inserisci le note">' + note + '</textarea>' +
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
                            '<input  type =\'datetime\' id=\"endEventDate\" class=\"form-control\"' +
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
                            '<option value="notNotify">Non Inviare la Notifica</option>' +
                            '<option value="yesNotify">Invia la Notifica</option>' +
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
                            '<div class="form-group form-group-default required">' +
                            '<label for="cloneDetail">Cancellazione</label>' +
                            '<div><p>Cancella il Dettaglio</p></div>' +
                            '<input type="button" class="btn-success" id="cloneDetail" name="cloneDetail" value="Clona il Dettaglio del Piano"' +
                            '</div>' +
                            '<input type="hidden" id="editorialPlanDetailId" name="editorialPlanDetailId" value=\"' + editorialPlanDetailId + '\"/>'
                    });


                    $.ajax({
                        method: 'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'EditorialPlan'
                        },
                        dataType: 'json'
                    }).done(function (res2) {
                        var select = $('#editorialPlanId');
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
                                selectize.setValue(editorialPlanId);
                            }
                        });

                    });
                    $.ajax({
                        url: '/blueseal/xhr/SelectFacebookCampaignAjaxController',
                        method: 'GET',
                        data: {
                            editorialPlanId: editorialPlanId
                        },
                        dataType: 'json'
                    }).done(function (res) {
                        console.log(res);
                        let campaignName = $('#campaignName');
                        //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
                        campaignName.selectize({
                            valueField: 'idCampaign',
                            labelField: 'nameCampaign',
                            searchField: ['nameCampaign'],
                            options: res,
                            render: {
                                item: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                                        '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                                        '</div>'
                                },
                                option: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                                        '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                                        '</div>'
                                }
                            }, onInitialize: function () {
                                let selectize = this;
                                selectize.setValue(facebookCampaignId);
                            }
                        });
                    });
                    $.ajax({
                        url: '/blueseal/xhr/SelectFacebookAdSetAjaxController',
                        method: 'get',
                        data: {
                            campaignId: facebookCampaignId,
                            editorialPlanId: editorialPlanId
                        },
                        dataType: 'json'
                    }).done(function (res) {
                        console.log(res);
                        let groupAdsName = $('#groupAdsName');
                        //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
                        groupAdsName.selectize({
                            valueField: 'idAdSet',
                            labelField: 'nameAdSet',
                            searchField: ['nameAdSet'],
                            options: res,
                            render: {
                                item: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                                        '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                                        '</div>'
                                },
                                option: function (item, escape) {
                                    return '<div>' +
                                        '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                                        '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                                        '</div>'
                                }
                            }, onInitialize: function () {
                                let selectize = this;
                                selectize.setValue(groupInsertionId);
                            }
                        });
                    });

                    var photoUrl1 = [];
                    bsModal2.addClass('modal-wide');
                    bsModal2.addClass('modal-high');
                    let dropzone = new Dropzone("#dropzoneModal", {
                        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

                        maxFilesize: 5,
                        maxFiles: 100,
                        parallelUploads: 10,
                        acceptedFiles: "image/jpeg,video/*",
                        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
                        uploadMultiple: true,
                        sending: function (file1, xhr, formData) {

                        }
                    });
                    dropzone.on('addedfile', function (file1) {
                        okButton.attr("disabled", "disabled");
                        let urlimage1 = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
                        let filename1 = file1.name;
                        let image1 = urlimage1 + filename1;
                        photoUrl1.push(image1);

                    });
                    dropzone.on('queuecomplete', function () {
                        okButton.removeAttr("disabled");
                        $(document).trigger('bs.load.photo');
                    });
                    var selected = $('#editorialPlanId').selectize();
                    var selectize = selected[0].selectize;

                    selectize.setValue(editorialPlanId);
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
                                        photoUrl: photoUrl,
                                        status: status,
                                        socialId: socialId,
                                        title: title,
                                        note: note


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
                    $("#cloneDetail").click(function () {
                        if (confirm("Sei Sicuro di Clonare il Dettaglio del Piano Editoriale?")) {
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
                                    url: "/blueseal/xhr/EditorialPlanDetailCloneAjaxController",
                                    type: "put",
                                    data: {
                                        editorialPlanId: editorialPlanId,
                                        editorialPlanDetailId: editorialPlanDetailId,

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
                        var photo;
                        if (photoUrl1.length == 0) {
                            photo = photoUrl;
                        } else {
                            photo = photoUrl1;
                        }

                        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
                        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
                        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
                        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
                        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
                        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
                        start = $('#startEventDate').val();
                        end = $('#endEventDate').val();
                        const data = {
                            title: $('#titleEvent').val(),
                            start: start,
                            end: end,
                            argument: $('#editorialPlanArgumentId').val(),
                            description: $('#description').val(),
                            linkDestination: $('#linkDestination').val(),
                            note: $('#note').val(),
                            isVisibleNote: isVisNote,
                            photoUrl: photo,
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
                            isVisibleBodyEvent: isVisBody,
                            lifetime_budget: $('#lifetime_budget').val(),
                            buying_type: $('#buying_type').val(),
                            objective: $('#objective').val(),


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
                                photoUrl: photoUrl,
                                status: status,
                                bodyEvent: bodyEvent,
                                isVisibleBodyEvent: isVisibleBodyEvent,
                                socialId: socialId,
                                notifyEmail: notifyEmail,
                                buying_type: $('#buying_type').val(),
                                objective: $('#objective').val(),
                                typeBudget: $('#typeBudget').val(),
                                lifetime_budget: $('#lifetime_budget').val(),
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
                            photoUrl: photoUrl,
                            status: status,
                            bodyEvent: bodyEvent,
                            isVisibleBodyEvent: isVisibleBodyEvent,
                            socialId: socialId,
                            notifyEmail: notifyEmail,
                            linkDestination: $('#linkDestination').val(),
                            campaignName: $('#campaignName').val(),
                            groupAdsName: $('#groupAdsName').val(),
                            lifetime_budget: $('#lifetime_budget').val(),
                            buying_type: $('#buying_type').val(),
                            objective: $('#objective').val(),
                        },
                        success: function () {
                            calendar.fullCalendar('refetchEvents');
                            alert("Dettaglio Piano Editoriale Aggiornato");

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
                    var linkimg1 = "";
                    var link = event.photoUrl.split(",");
                    link.forEach(function (element) {

                        var view = $('#calendar').fullCalendar('getView');
                        if (view.name == 'month') {
                            linkimg1 = linkimg1 + ' <img width="100px" src="' + element + '">';
                        } else if (view.name == 'day') {
                            linkimg1 = linkimg1 + ' <img width="450px" src="' + element + '">';
                        } else {
                            linkimg1 = linkimg1 + ' <img width="450px" src="' + element + '">';
                        }


                    });

                    tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:#ffffff;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;">' + 'titolo: ' + ': ' + event.title + '</br>' + 'testo: ' + ': ' + event.bodyEvent + '</br>' + linkimg1 + '</div>';


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
                eventClick: function (event) {
                    var editorialPlanDetailId = event.id;
                    window.location.href = '/blueseal/editorial/modifica-post/' + editorialPlanDetailId;
                }


            });


        }
    }


})
(jQuery);

function addCampaign() {
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
        $('#facebookCampaignId').val(res);
    }).fail(function (res) {

    }).always(function (res) {

    });

}

function updateCampaign() {
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
        $('#facebookCampaignId').val(res);
    }).fail(function (res) {
    }).always(function (res) {

    });

}

