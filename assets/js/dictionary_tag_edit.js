$(document).on('change', $('name[tagId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            tagId: changed.val(),
            id: changed.data('pid')
        }
    });
});
