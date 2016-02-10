$(document).on('change', $('name[brandId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            brandId: changed.val(),
            id: changed.data('pid')
        }
    });
});