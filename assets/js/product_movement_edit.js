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