$(document).on('change', $('name[brandId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            brandId: encodeURIComponent(changed.val()),
            id: encodeURIComponent(changed.data('pid'))
        }
    });
});