(function ($) {



    $('#searchGender').on('input', function() {
        let searchTerm = $.trim(this.value);
        $('.sg').each(function() {
            if (searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).toggle($(this).filter('[data-searchgender*="' + searchTerm + '"]').length > 0);
            }
        });
    });

    $('#searchMacroCategory').on('input', function() {
        let searchTerm = $.trim(this.value);
        $('.smcg').each(function() {
            if (searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).toggle($(this).filter('[data-searchmacrocategory*="' + searchTerm + '"]').length > 0);
            }
        });
    });

    $('#searchCategory').on('input', function() {
        let searchTerm = $.trim(this.value);
        $('.sc').each(function() {
            if (searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).toggle($(this).filter('[data-searchcategory*="' + searchTerm + '"]').length > 0);
            }
        });
    });

    $('#searchMaterial').on('input', function() {
        let searchTerm = $.trim(this.value);
        $('.sm').each(function() {
            if (searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).toggle($(this).filter('[data-searchmaterial*="' + searchTerm + '"]').length > 0);
            }
        });
    })
})(jQuery);