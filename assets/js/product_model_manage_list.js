(function ($) {


    $("#searchGender").keyup(function(event) {
        let searchTerm = $.trim(this.value);
        $('.sg').each(function() {
            if(event.keyCode === 13){
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchgender*="' + searchTerm + '"]').length > 0);}
            }
        });
    });

    $("#searchMacroCategory").keyup(function(event) {
        let searchTerm = $.trim(this.value);
        $('.smcg').each(function() {
            if(event.keyCode === 13){
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchmacrocategory*="' + searchTerm + '"]').length > 0);}
            }
        });
    });

    $("#searchCategory").keyup(function(event) {
        let searchTerm = $.trim(this.value);
        $('.sc').each(function() {
            if(event.keyCode === 13){
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchcategory*="' + searchTerm + '"]').length > 0);}
            }
        });
    });

    $("#searchMaterial").keyup(function(event) {
        let searchTerm = $.trim(this.value);
        $('.sm').each(function() {
            if(event.keyCode === 13){
                if(searchTerm.length < 1) { $(this).show() } else {$(this).toggle($(this).filter('[data-searchmaterial*="' + searchTerm + '"]').length > 0);}
            }
        });
    });

})(jQuery);