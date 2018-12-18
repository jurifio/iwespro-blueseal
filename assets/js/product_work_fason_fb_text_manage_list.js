;(function () {

    Dropzone.autoDiscover = false;
    $(document).ready(function () {


        $('.dropzone').each(function () {

            let dictDefaultMessage = $(this).attr('data-textmessagearea');
            let type = $(this).attr('data-typeajax');

            let dropzoneInterationPost = new Dropzone(`#${$(this).attr('id')}`, {
                url: "/blueseal/xhr/ProductWorkFasonFbTextManageImagePhotoAjaxManage",
                maxFilesize: 5,
                maxFiles: 1,
                parallelUploads: 1,
                acceptedFiles: "image/*",
                dictDefaultMessage: dictDefaultMessage,
                uploadMultiple: true,
                sending: function (file, xhr, formData) {
                    let activePTM = $('#mainBatchDiv').find('.active').attr('id');
                    formData.append("productBatchId", window.location.href.substring(window.location.href.lastIndexOf('/') + 1));
                    formData.append("type", type);
                    formData.append("pbTmId", activePTM);
                },
                success: function(file, response){
                    let type = response.substring(0,3) === 'Err' ? 'warning' : 'success';

                    new Alert({
                        type: type,
                        message: response
                    }).open();

                    window.setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                }
            });

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

            setTimeout(function () {
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