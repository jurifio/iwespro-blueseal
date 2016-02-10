$(document).on('change', $('name[seasonId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            seasonId: changed.val(),
            id: changed.data('pid')
        }
    });
});
