;(function () {


    Dropzone.autoDiscover = false;
    $(document).ready(function () {


        //set initial state.
        $('.photo').val(this.checked);

        $('.photoSect').each(function () {
            $(this).empty().append(`<button class="saveB" id="save-${$(this).attr('id').split('-')[1]}">Salva</button>`);
        });

        $('.checkPhoto').change(function() {
            let batchTextManageId = $(this).attr('id').split('-')[1];
            if(this.checked) {
                $(`#photoSect-${batchTextManageId}`).empty().append(`
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
                        formData.append("productBatchId", window.location.href.substring(window.location.href.lastIndexOf('/') + 1));
                        formData.append("productBatchTextManageId", batchTextManageId);
                        formData.append("txt", $(`#fasonTxt-${batchTextManageId}`).val());
                    }
                });

                dropzone.on('queuecomplete',function(){
                    window.location.reload();
                    $(document).trigger('bs.load.photo');
                });
            } else {
               // $('#photo').val(this.checked);
                $(`#photoSect-${batchTextManageId}`).empty().append(`<button class="saveB" id="save-${batchTextManageId}">Salva</button>`);
            }

        });

    });

    $(document).on('click', '.saveB', function () {

            let textManageId = $(this).attr('id').split('-')[1];
            const data = {
                productBatchId: window.location.href.substring(window.location.href.lastIndexOf('/') + 1),
                productBatchTextManageId: textManageId,
                txt: $(`#fasonTxt-${textManageId}`).val()
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

        let activePTM = $('#mainBatchDiv').find('.active').attr('id');

        let bsModal = new $.bsModal('Conferma Normalizzazione Prodotti', {
            body: '<p>Confermi la fine della procedura di normalizzazione?</p>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                textManage: activePTM,
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

        let workCategoryId = $('.workCategoryId').val();

        let activePTM = $('#mainBatchDiv').find('.active').attr('id');

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
                textManage: activePTM,
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

        let activePTM = $('#mainBatchDiv').find('.active').attr('id');

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
                textManage: activePTM,
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