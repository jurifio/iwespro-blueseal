$(document).on('bs.detailTranslation.changeTargetLanguage', function (event, select, selectObj) {
    var $t = $('table[data-datatable-name]');
    $t.data('useTargetLang', select.val());
    var dt = $t.DataTable();
    dt.draw();
});

$(document).on('bs.detailTranslation.filterByQty', function () {
    var $t = $('table[data-datatable-name]');
    $t.data('mustHaveQty', 1);
    var dt = $t.DataTable();
    dt.draw();
});

$(document).on('blur', '.dt-input', function () {
    var $formControl = $(this).parent();
    $formControl.addClass('loading');
    $.ajax({
        url: $('table[data-datatable-name]').data('url') + "/DetailBatchTranslateListAjaxController",
        type: "PUT",
        data: {
            lang: $(this).data('lang'),
            name: $(this).val(),
            id: $(this).attr('id').split('_')[1]
        }
    }).done(function (data) {
        $formControl.removeClass('loading');
        $formControl.css('box-shadow', 'inset 0 0 1px 1px #009900');
        $formControl.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
            $formControl.css('box-shadow', 'inset 0 0 1px #c0c0c0');
        });
    }).fail(function (data) {
        $formControl.removeClass('loading');
        $formControl.css('box-shadow', 'inset 0 0 1px 1px #990000');
        $formControl.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
            $formControl.css('box-shadow', 'inset 0 0 1px #c0c0c0');
        });
    });
});


$(document).on('bs.end.work.product.detail.translation', function () {

    let selectedProductDetails = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedProductDetails.push(v.id);
    });

    let bsModal = new $.bsModal('Modifica', {
        body: '<p>Confermi la fine della procedura di traduzione per i dettagli selezionati?</p>'
    });

    let url = window.location.href;
    let lang = url.substr(url.lastIndexOf('/') + 1).charAt(0);

    let urlObj = new URL(url);
    let batchId = urlObj.searchParams.get("pbId");

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            details: selectedProductDetails,
            lang: lang,
            batchId: batchId
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductDetailTranslationBatchManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
            });
            bsModal.showOkBtn();
        });
    });
});


    $(document).on('bs.delete.product.detail.translation', function () {

        let selectedProductDetails = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProductDetails.push(v.id);
        });

        let bsModal = new $.bsModal('Elimina', {
            body: '<p>Confermi di eliminare i dettagli dal lotto?</p>'
        });

        let url = window.location.href;
        let lang = url.substr(url.lastIndexOf('/') + 1).charAt(0);

        let urlObj = new URL(url);
        let batchId = urlObj.searchParams.get("pbId");

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                details: selectedProductDetails,
                lang: lang,
                batchId: batchId
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductDetailTranslationBatchManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
