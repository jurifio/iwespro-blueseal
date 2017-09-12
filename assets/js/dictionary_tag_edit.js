$(document).on('change', $('name[tagId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            tagId: encodeURIComponent(changed.val()),
            id: encodeURIComponent(changed.data('pid'))
        }
    });
});
