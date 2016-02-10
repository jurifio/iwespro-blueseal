$(document).on('change', $('name[sizeId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            sizeId: changed.val(),
            id: changed.data('pid')
        }
    });
});
