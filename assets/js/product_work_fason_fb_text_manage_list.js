;(function () {

    Dropzone.autoDiscover = false;
    $(document).ready(function () {


        let dropzoneInterationPost = new Dropzone("#dropzoneModalInterationPost", {
            url: "/blueseal/xhr/ProductWorkFasonFbTextManageImagePhotoAjaxManage",
            maxFilesize: 5,
            maxFiles: 1,
            parallelUploads: 1,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui le foto per l'interazione sui post",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
                formData.append("productBatchId", $('#productBatchId').val());
                formData.append("type", 'interationPost');
            }
        });

        dropzoneInterationPost.on('queuecomplete', function () {
            $(document).trigger('bs.load.photo');
        });

        let dropzoneLike = new Dropzone("#dropzoneModalLike", {
            url: "/blueseal/xhr/ProductWorkFasonFbTextManageImagePhotoAjaxManage",
            maxFilesize: 5,
            maxFiles: 1,
            parallelUploads: 1,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui le foto per il like sulla pagina",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
                formData.append("productBatchId", $('#productBatchId').val());
                formData.append("type", 'pageLike');
            }
        });

        dropzoneLike.on('queuecomplete', function () {
            $(document).trigger('bs.load.photo');
        });

        let dropzonePost = new Dropzone("#dropzoneModalPost", {
            url: "/blueseal/xhr/ProductWorkFasonFbTextManageImagePhotoAjaxManage",
            maxFilesize: 5,
            maxFiles: 1,
            parallelUploads: 1,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui le foto il post sul diario",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
                formData.append("productBatchId", $('#productBatchId').val());
                formData.append("type", 'newPost');
            }
        });

        dropzonePost.on('queuecomplete', function () {
            $(document).trigger('bs.load.photo');
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