$(document).on('bs.detailTranslation.changeTargetLanguage', function(event,select,selectObj) {
    var $t = $('table[data-datatable-name]');
    $t.data('useTargetLang',select.val());
    var dt = $t.DataTable();
    dt.draw();
});

$(document).on('bs.detailTranslation.filterByQty',function() {
    var $t = $('table[data-datatable-name]');
    $t.data('mustHaveQty',1);
    var dt = $t.DataTable();
    dt.draw();
});

$(document).on('blur','.dt-input',function() {
    var $formControl = $(this).parent();
    $formControl.addClass('loading');
    $.ajax({
        url: $('table[data-datatable-name]').data('url')+"/DetailTranslateListAjaxController",
        type: "PUT",
        data: {
            lang: $(this).data('lang'),
            name: $(this).val(),
            id: $(this).attr('id').split('_')[1]
        }
    }).done(function(data) {
        $formControl.removeClass('loading');
        $formControl.css('box-shadow','inset 0 0 1px 1px #009900');
        $formControl.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',function() {
            $formControl.css('box-shadow','inset 0 0 1px #c0c0c0');
        });
    }).fail(function(data) {
        $formControl.removeClass('loading');
        $formControl.css('box-shadow','inset 0 0 1px 1px #990000');
        $formControl.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',function() {
            $formControl.css('box-shadow','inset 0 0 1px #c0c0c0');
        });
    });
});

$(document).on('bs.detail.associate.product.batch', function () {

    let pIds = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    $.each(selectedRows, function (k, v) {
        pIds.push(v.DT_RowId.split('__')[1]);
    });

    if(selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un dettaglio"
        }).open();
        return false;
    }

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Lang',
            condition: {isActive: 1}
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#lang');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });

    let bsModal = new $.bsModal('Assegna nomi prodotto/lingua al lotto', {
        body: `
                <p>Inserisci il numero del lotto</p>
                <input type="number" min="1" id="productBatchId">
                <p>Seleziona la lingua</p>
                <select id="lang" name="lang"></select>
                `
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            productBatchId: $('#productBatchId').val(),
            langId: $('#lang').val(),
            pIds: pIds
        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductBatchDetailsTranslateManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Errore grave');
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });

});