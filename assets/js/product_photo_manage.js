/**
 * Created by Fabrizio Marconi on 12/10/2015.
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
        url: '/blueseal/xhr/ProductPhotoAjaxManage',
        data: {
            "id" : $.QueryString["id"],
            "productVariantId" : $.QueryString["productVariantId"]
        }
    }).done(function (content) {
        $('#selectable').html(content);
    })
});

$(document).on('bs.add.photo', function (e){
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Carica Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    var bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
        '<div class="fallback">'+
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    body.html(bodyContent);
    var dropzone = new Dropzone("#dropzoneModal",{
        url: "/blueseal/xhr/ProductPhotoAjaxManage",
        maxFilesize: 4,
        maxFiles: 10,
        parallelUploads: 10,
        acceptedFiles: "image/jpeg",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: false,
        sending: function(file, xhr, formData) {
            formData.append("id", $.QueryString["id"]);
            formData.append("productVariantId", $.QueryString["productVariantId"]);
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
        url: '/blueseal/xhr/ProductPhotoAjaxManage',
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
            url: '/blueseal/xhr/ProductPhotoAjaxManage',
            data: {
                "id" : $.QueryString["id"],
                "productVariantId" : $.QueryString["productVariantId"],
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