/**
 * Created by Fabrizio Marconi on 25/06/2015.
 */
(function($) {
    $(".master").on('keyup', function(){
        var target = $(this).data('target');
        var value = $(this).val();

        $( "[id^="+target+"]").each(function(){
            $(this).val(value);
        })
    });
})(jQuery);