$(document).on('focusout', $('name[nameId]').val(), function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            nameId: changed.val(),
            id: changed.data('pid'),
            lang: changed.data('lang')
        }
    });
});