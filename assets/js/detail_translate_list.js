$(document).on('bs.detailTranslation.changeTargetLanguage', function(event,select,selectObj) {
    var $t = $('table[data-datatable-name]');
    $t.data('useTargetLang',select.val());
    var dt = $t.DataTable();
    dt.draw();
});

$(document).on('blur','.dt-input',function() {
    var $formControl = $(this).parent();
    $formControl.addClass('loading');
    $.ajax({
        url: $('table[data-datatable-name]').data('url')+"/CDetailTranslationController",
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