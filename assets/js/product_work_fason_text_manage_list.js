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
                    url: "/blueseal/xhr/ProductWorkFasonTextManageImagePhotoAjaxManage",
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

                dropzone.on('addedfile',function(){
                    okButton.attr("disabled", "disabled");
                });
                dropzone.on('queuecomplete',function(){

                    okButton.removeAttr("disabled");
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
                productBatchId: $('#productBatchId').val(),
                txt: $('#fasonTxt').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/ProductWorkBatchFasonTextManageAjaxController',
                    data: data
                }).done(function (res) {
                    new Alert({
                        type: "success",
                        message: res
                    }).open();

                    setTimeout(function() {
                        window.location.reload();
                    }, 5000);
                }).fail(function (res) {
                    new Alert({
                        type: "error",
                        message: "Errore"
                    }).open();
                    return false;
                })
        });

    $(document).on('bs.end.text.manage', function () {

        let productBatchId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let bsModal = new $.bsModal('Conferma Normalizzazione Prodotti', {
            body: '<p>Confermi la fine della procedura di normalizzazione?</p>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                batchId: productBatchId,
                type: 'fasonOperation'
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductWorkBatchFasonTextManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });


    $(document).on('bs.status.text.manage', function () {

        let workCategoryId = $('#workCategoryId').val();
        let productBatchId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let bsModal = new $.bsModal('Cambia stato della lavorazione', {
            body: `<div>
                        <p>Seleziona lo stato</p>
                        <select id="categories">
                        </select>
                   </div>`
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategorySteps',
                condition: {workCategoryId: workCategoryId}
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#categories');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                batchId: productBatchId,
                step: $('#categories').val(),
                type: 'masterOperation'
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductWorkBatchFasonTextManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });


    $(document).on('bs.note.text.manage', function () {

        let productBatchId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let bsModal = new $.bsModal('Aggiungi una nota per i prodotti selezionati', {
            body: `<p>Inserisci/accoda/sovrascrivi una nuova nota</p>
               <select style="display: block; margin: 0 auto" id="type">
               <option value="s">Inserisci/sovrascrivi</option>
               <option value="a">Accoda</option>
               </select>
               <textarea style="width: 400px; height: 400px;" id="newNote"></textarea>`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                batchId: productBatchId,
                note: $('#newNote').val(),
                type: $('#type').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductBatchTextManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });

})();