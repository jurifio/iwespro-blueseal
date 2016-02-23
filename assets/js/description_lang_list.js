$(document).on('focusout', $('name[descId]').val(), function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            nameId: changed.val(),
            id: changed.data('pid'),
            lang: changed.data('lang')
        }
    });
});

$(document).ready(function() {

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200
            });
        }
    });

});