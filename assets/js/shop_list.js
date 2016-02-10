$(document).on('click', $('name[shop]').val, function(event) {
    var changed =  $(event.target);
    var result = {
        status: 0
    };
    $.ajax({
        type: "GET",
        url: changed.data('action'),
        data: {
            shopId: changed.val()
        }
    }).done(function (response) {
        result = JSON.parse(response);
        window.location.replace('/blueseal/importatori/connettori/aggiungi?shopId='+result.shopId);
    });
});