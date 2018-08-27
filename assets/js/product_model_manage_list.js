(function ($) {


    $("#gndBtn").on('click', function () {
        let searchTerm = $.trim($("#searchGender").val());
        $('.sg').each(function() {
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchgender*="' + searchTerm + '"]').length > 0);}
        });
    });

    $("#mcrBtn").on('click', function () {
        let searchTerm = $.trim($("#searchMacroCategory").val());
        $('.smcg').each(function() {
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchmacrocategory*="' + searchTerm + '"]').length > 0);}
        });
    });

    $("#crBtn").on('click', function () {
        let searchTerm = $.trim($("#searchCategory").val());
        $('.sc').each(function() {
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchcategory*="' + searchTerm + '"]').length > 0);}
        });
    });

    $("#mBtn").on('click', function () {
        let searchTerm = $.trim($("#searchMaterial").val());
        $('.sm').each(function() {
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchmaterial*="' + searchTerm + '"]').length > 0);}
        });
    });

})(jQuery);