;(function () {

    $(document).ready(function () {


        //set initial state.
        $('#photo').val(this.checked);
        $('#photoSect').empty().append(`<button id="save">Salva</button>`);

        $('#photo').change(function() {
            if(this.checked) {
                $('#photoSect').empty().append(`
                 <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data"
                       name="dropzonePhoto" action="POST">
                     <div class="fallback">
                         <input name="file" type="file" multiple/>
                     </div>
                 </form>
                `);

                let dropzone = new Dropzone("#dropzoneModal",{
                    url: "/blueseal/xhr/ProductWorkTextManageImagePhotoAjaxManage",
                    maxFilesize: 5,
                    maxFiles: 5,
                    parallelUploads: 5,
                    acceptedFiles: "image/*",
                    dictDefaultMessage: "Trascina qui le foto",
                    uploadMultiple: true,
                    sending: function(file, xhr, formData) {
                        formData.append("productBatchId", $('#productBatch').val());
                        formData.append("theme", $('#theme').val());
                        formData.append("description", $('#description').val());
                    }
                });

                dropzone.on('queuecomplete',function(){
                    $(document).trigger('bs.load.photo');
                });
            } else {
               // $('#photo').val(this.checked);
                $('#photoSect').empty().append(`<button id="save">Salva</button>`);
            }

        });

    });

    $(document).on('click', '#save', function () {
            const data = {
                productBatchId: $('#productBatch').val(),
                theme: $('#theme').val(),
                description: $('#description').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/ProductWorkBatchTextManageAjaxController',
                    data: data
                }).done(function (res) {
                    new Alert({
                        type: "success",
                        message: res
                    }).open();

                    setTimeout(function() {
                        window.location.href = '/blueseal/work/lotti';
                    }, 5000);

                }).fail(function (res) {
                    new Alert({
                        type: "error",
                        message: "Errore"
                    }).open();
                    return false;
                }).always(function (res) {
                });
        });

})();