$(document).on('draw.dt', function() {

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200
            });
        }
    });

});

$(document).on('click', $('name[saveDescription]').val(), function(event) {
   alert('ciao');
});