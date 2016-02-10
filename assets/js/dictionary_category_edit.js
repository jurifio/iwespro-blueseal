$(document).on('change', $('name[categoryId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            categoryId: changed.val(),
            id: changed.data('pid')
        }
    });
});
