/**
 * Created by Fabrizio Marconi on 11/09/2015.
 */
(function($) {
    $('[data-order]').each(function(){
        loadLine($(this));
    });
})(jQuery);

/**
 * @param lineIn
 */
function loadLine(lineIn) {
    var line = lineIn;
    var url = line.data('url');
    var orderLine = line.data('order');
    $(this).html('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            "order": orderLine
        }
    }).done(function(content) {
        line.html(content).fadeIn();
    }).fail(function(content){
        line.html('<i class="fa fa-times"></i>').hide().css('background-color','red').fadeIn();
    }).always(function(content){
    });
}

/**
 * @param button
 * @returns {string}
 */
function reloadLineFromButton(button){
    loadLine($(button).parent().parent());
}

function reloadLineFromForm(form){
    loadLine($(form).parent().parent());
}