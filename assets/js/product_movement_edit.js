$(document).on('bs.movement.edit', function() {
    var submitBtn = $('.mag-submit-btn');
    if (submitBtn.length) {
        submitBtn.trigger('click');
    }
});

$(document).ready(function(){
    var code = $_GET.get('code');
    if (false != code) {
        var prods = [];
        prods = code.split(',');
    }


    $('.form-container').bsCatalog({
        searchField: true,
        product: prods
    });
});