(function ($) {
    $(document).ready(function () {
        glyph_opts = {
            preset: "bootstrap3",
            map: {
                expanderClosed: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                expanderOpen: "glyphicon glyphicon-menu-down"  // glyphicon-minus-sign
            }
        };
        var datailsContainer = $('#categoryDetails');
        $("#categoryTree").fancytree({
            extensions: ["dnd", "edit", "glyph", "wide"],
            glyph: glyph_opts,
            checkbox: false,
            source: {
                url: "/blueseal/xhr/CategoryTreeController"
            },
            activate: function (event,data) {
                datailsContainer.html('<img src="/assets/img/ajax-loader.gif" />');
                Pace.ignore(function () {
                    $.ajax({
                        url:"/blueseal/xhr/ProductCategoryController",
                        data: {
                            id: data.node.key
                        },
                        dataType: 'json'
                    }).done(function (res) {
                        datailsContainer.html('<span>slug</span><br /><span>'+res.slug+'</span>');
                    })
                })
            }

        });
    });
})(jQuery);