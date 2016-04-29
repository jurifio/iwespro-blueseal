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
    },f).done(function() {
		    window.location.href("/blueseal/blog");
	    });

});