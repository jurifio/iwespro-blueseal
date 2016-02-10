$('textarea.summer').summernote({
    lang: "it-IT",
    height: 300
});

$('[data-toggle="popover"]').popover();

$('#cover').on('click',function() {
    $('[data-json="PostTranslation.coverImage"]').click();
});

$('[data-json="PostTranslation.coverImage"]').on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cover').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(document).on('bs.save.post', function() {

    var f = new FormData();

    $('[data-json]').each(function() {
        if ($(this).is(':file')) {
            f.append($(this).data('json'), this.files[0])
        } else {
            f.append($(this).data('json'),$(this).val());
        }
    });

    $.ajaxForm({
        url: '#',
        type: 'post'
    },f);

});

$(document).on('bs.add.gallery', function() {

    var m = new Modal();
    m.setTitle('Crea una gallery');
    m.show();

});

$(document).on('bs.add.youtube', function() {

    var m = new Modal();
    m.setTitle('Aggiungi un video');
    m.show();

});

$(document).on('bs.add.productslider', function() {

    var m = new Modal();
    m.setTitle('Aggiungi uno slideshow di prodotti');
    m.show();

});