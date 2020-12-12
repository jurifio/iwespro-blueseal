var photoUrl='';
$(document).on('bs.submenu.add', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiunta SubMenu');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();



    $.ajax({
        type: "POST",
        url: "#",
        data: $('form').serialize()
    }).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
        okButton.off().on('click', function() {
            bsModal.modal('hide');
            okButton.off();
            window.location.href = '/blueseal/submenu/modifica/'+content;
        });
    }).fail(function(){
        body.html('Errore grave');
        bsModal.modal();
    });
});


$('#chooseOperation').change(function () {
   if ($('#chooseOperation').val()=="1"){
       $('#divUploadImage').removeClass('hide');
       $('#divUploadImage').addClass('show');
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
           $.refreshDataTable();
       });
       cancelButton.remove();
       let bodyContent =
           '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
           '<div class="fallback">'+
           '<input name="file" type="file" multiple />' +
           '</div>' +
           '</form>';

       body.html(bodyContent);
       let dropzone = new Dropzone("#dropzoneModal",{
           url: "/blueseal/xhr/SubmenuImageUploadAjaxController",
           maxFilesize: 80,
           maxFiles: 10,
           parallelUploads: 1,
           acceptedFiles: "image/jpeg",
           dictDefaultMessage: "Trascina qui l'immagine da impostare come cover",
           uploadMultiple: true,
           sending: function(file, xhr, formData) {

           }
       });

       dropzone.on('addedfile',function(file){
          // okButton.attr("disabled", "disabled");
           //photoUrl =  file;
          let urlimage = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
           let filename = file.name;
           let image = urlimage + filename;
           photoUrl=image;
            $('#photoUrl').val(photoUrl);
       });
       dropzone.on('queuecomplete',function(){
           $(document).trigger('bs.load.photo');

       });
   } else{
       $('#divUploadImage').removeClass('show');
       $('#divUploadImage').addClass('hide');

   }
});
$('#typeId').on('change',  function () {
    let typeId=$('#typeId').val();
    switch(typeId){
        case "1":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default selectize-enabled">
                                                <label for="elementId">Seleziona la Pagina </label>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                <select class="full-width selectpicker"
                                                        placeholder="Seleziona la Pagina"
                                                        data-init-plugin="selectize" tabindex="-1" title="Pagina"
                                                        name="elementId" id="elementId"
                                                        required="required">
                                                </select>
                                            </div>   ​<input type="hidden" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value="page"/>
            `);
            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'FixedPage',
                    condition: {langId: 1}
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('select[name=\"elementId\"]');
                if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'title',
                    searchField: ['title'],
                    options: res2,
                });
            });
            break;
        case "2":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default">
                                                <label for="captionLink">Link Personalizzato</label>
                                                ​<input type="text" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value=""/>
                                            </div>
                                            <input type="hidden" class="form-control" id="elementId"
                                                        name="elementId"
                                                        required="required"
                                                        value="0"/>
                                            </div>
            `);
            break;
        case "3":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default selectize-enabled">
                <label for="elementId">Seleziona la Categoria</label>
                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                <select class="full-width selectpicker"
                        placeholder="Seleziona la Categoria"
                        data-init-plugin="selectize" tabindex="-1" title="Categoria"
                        name="elementId" id="elementId"
                        required="required">
                </select>
            </div>   ​<input type="hidden" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value="category"/>
            `);
            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'ProductCategory'
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('select[name=\"elementId\"]');
                if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'slug',
                    searchField: ['slug'],
                    options: res2,
                });
            });
            break;
        case "4":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default selectize-enabled">
                <label for="elementId">Seleziona il Tag</label>
                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                <select class="full-width selectpicker"
                        placeholder="Seleziona il Tag"
                        data-init-plugin="selectize" tabindex="-1" title="Tag"
                        name="elementId" id="elementId"
                        required="required">
                </select>
            </div>
               ​<input type="hidden" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value="tag"/>
            `);
            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Tag'
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('select[name=\"elementId\"]');
                if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'slug',
                    searchField: ['slug'],
                    options: res2,
                });
            });
            break;
        case "5":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default selectize-enabled">
                <label for="elementId">Seleziona il Tag Esclusivo</label>
                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                <select class="full-width selectpicker"
                        placeholder="Seleziona il Tag Esclusivo"
                        data-init-plugin="selectize" tabindex="-1" title="TagExclusive"
                        name="elementId" id="elementId"
                        required="required">
                </select>
            </div>
             ​<input type="hidden" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value="tagExclusive"/>
            `);
            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'TagExclusive'
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('select[name=\"elementId\"]');
                if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'exclusiven',
                    searchField: ['exclusiven'],
                    options: res2,
                });
            });
            break;
        case "6":
            $('#selectElement').removeClass('hide');
            $('#selectElement').empty();
            $('#selectElement').append(`
            <div class="form-group form-group-default">
                                              
                                                ​<input type="hidden" class="form-control" id="captionLink"
                                                        name="captionLink"
                                                        required="required"
                                                        value="brands"/>
                                            </div>
                                            <input type="hidden" class="form-control" id="elementId"
                                                        name="elementId"
                                                        required="required"
                                                        value="3"/>
                                            </div>
            `);
            break;
    }
});
