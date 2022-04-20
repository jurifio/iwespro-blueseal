/**
 * Revisioned by Juri Fiorani after Created by Fabrizio Marconi on 12/10/2015.
 */
var el = document.getElementById("selectable");
var photoOrderList = Sortable.create(el, {
    animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
    draggable: ".draggable", // Specifies which items inside the element should be sortable
    filter: '.js-remove',
    onFilter: function (evt) {
        $(document).trigger('bs.photo.delete', evt);
    }
});

$(document).ready(function() {
    $(this).trigger('bs.load.photo');
});

$(document).on('bs.load.photo',function(){
    $.ajax({
        type: 'GET',
        url: '/blueseal/xhr/ProductSearchPhotoAjaxManage',
        data: {
            "id" : $('#id').val(),
            "productVariantId" : $('#productVariantId').val()
        }
    }).done(function (content) {
        $('#selectable').html(content);
    })
});


$(document).on('bs.add.photo', function (e){
    var bsModal = $('#bsModal');

    var header = bsModal.find('.modal-header h4');
    var body = bsModal.find('.modal-body');
    var cancelButton = bsModal.find('.modal-footer .btn-default');
    var okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();

    header.html('Carica Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    let bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
        '<div class="fallback">'+
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    body.html(bodyContent);
    let  dropzone = new Dropzone("#dropzoneModal",{
        url: "/blueseal/xhr/ProductSearchPhotoAjaxManage",
        maxFilesize: 4,
        maxFiles: 10,
        parallelUploads: 10,
        acceptedFiles: "image/*",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui (consigliato MAX 200KB)",
        uploadMultiple: false,
        sending: function(file, xhr, formData) {
            formData.append("id", $('#id').val());
            formData.append("productVariantId", $('#productVariantId').val());
        }
    });

    dropzone.on('addedfile',function(){
        okButton.attr("disabled", "disabled");
    });
    dropzone.on('queuecomplete',function(){
        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
    });
});
$(document).on('bs.take.photo', function (e){
    var bsModal = $('#bsModal');
    var header = bsModal.find('.modal-header h4');
    var body = bsModal.find('.modal-body');
    var cancelButton = bsModal.find('.modal-footer .btn-default');
    var okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();
    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');

    header.html('Scatta Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    let bodyContent ='<button id="start-camera">Stato Camera Avviata</button>'+
    '<video  muted id="videoPhoto" width="225" height="300" autoplay playsinline="true"></video>'+
    '<button id="click-photo">Scatta</button>'+
    '<canvas id="canvasfiga" width="1125" height="1500"></canvas>';


    body.html(bodyContent);
    let camera_button = document.querySelector("#start-camera");
    let video = document.querySelector("#videoPhoto");
    let click_button = document.querySelector("#click-photo");
    let canvas = document.querySelector("#canvasfiga");
    camera_button.addEventListener('click', async function() {
        let stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        video.srcObject = stream;
    });

    click_button.addEventListener('click', function() {
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let image_data_url = canvas.toDataURL('image/jpeg');
        const data = {

            id: $('#id').val(),
            variantId: $('#productVariantId').val(),
            file: image_data_url,
            type: 2
        };


        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductSearchPhotoAjaxManage',
            data: data
        }).done(function (res) {
            $(document).trigger('bs.load.photo');
        }).fail(function (res) {
            $(document).trigger('bs.load.photo');
        }).always(function (res) {

        });


    });

});
$('#photoOrderForm').on('submit',function(e){
    e.preventDefault();
    var photos = $('#selectable > .draggable');
    var ids = [];
    for(var i=0;i<photos.length;i++){
        ids[i] = 'o'+i+'='+$(photos[i]).prop('id');
    }
    var serialized = $(this).serialize();
    ids = ids.join('&');

    $.ajax({
        type: 'PUT',
        url: '/blueseal/xhr/ProductSearchPhotoAjaxManage',
        data: serialized+'&'+ids
    }).done(function (content) {
        modal = new $.bsModal('Salvataggio Foto',
            {
                body: 'Salvataggio riuscito',
                isCancelButton: false
            });
    }).fail(function (content) {
        modal = new $.bsModal('Salvataggio Foto',
            {
                body: 'OOPS! Si è verificato un problema.<br /> Se il problema persiste contattare un amministratore',
                isCancelButton: false
            });
    });
});

$(document).on('bs.photo.delete', function (e, element) {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Cancellazione Foto');
    body.html('Sei sicuro di voler eliminare questa foto?');

    cancelButton.html('Annulla').off().on('click', function (e,evt) {
        bsModal.modal('hide');
        okButton.off();
    });

    okButton.html('Elimina Foto').off().on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: '/blueseal/xhr/ProductSearchPhotoAjaxManage',
            data: {
                "id" : $('#id').val(),
                "productVariantId" : $('#productVariantId').val(),
                "photoOrder" : $(element.item).prop('id')
            }
        }).done(function (content) {
            console.log(content);
        });
        bsModal.modal('hide');
        okButton.off();
        var el = photoOrderList.closest(element.item); // get dragged item
        el && $(el).fadeOut(400, function () {
            $(el).remove();
        })
    });
});

//handle: ".group", // Restricts sort start click/touch to the specified element