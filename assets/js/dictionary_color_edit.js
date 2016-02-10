$(document).on('change', $('name[colorId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            colorId: changed.val(),
            id: changed.data('pid')
        }
    });
});
