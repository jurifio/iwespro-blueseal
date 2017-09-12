$(document).on('change', $('name[seasonId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            seasonId: encodeURIComponent(changed.val()),
            id: encodeURIComponent(changed.data('pid'))
        }
    });
});
