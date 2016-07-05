function mq() {
    var toolbar = $('.bs-toolbar');
    var dropdown = $('.other-actions');
    var customToolbars = $('.bs-toolbar-custom');
    var toolbarCount = customToolbars.length;

    if ($.MatchMedia('(min-width:1276px)')) {

        var c = $('.other-actions .btn-group').length - 1;

        for (var i = c;i>=0;i--) {
            toolbar.append($('.other-actions .btn-group').eq(i));
        }

        $('.bs-toolbar-responsive').hide();
    }

    if ($.MatchMedia('(min-width:1166px) and (max-width:1275px)')) {
        dropdown.append(customToolbars.eq(toolbarCount - 1));
        $('.bs-toolbar-responsive').show();
    }

    if ($.MatchMedia('(max-width:1165px)')) {
        dropdown.append(customToolbars.eq(toolbarCount - 2));
        $('.bs-toolbar-responsive').show();
    }
}

function responsiveToolBar()
{
    /** var toolbar = $('.bs-toolbar');
    var dropdown = $('.other-actions');
    var customToolbars = $('.bs-toolbar > .bs-toolbar-custom');
    var toolbarCount = customToolbars.length;

    var toolbarWidth = $('.bs-toolbar').width();
    var customToolbarsWidth = (function(customToolbars) {
        var w = 0;
        customToolbars.each(function(k,v) {
            w += $(v).width();
        });
        return w;
    })(customToolbars);

    if (customToolbarsWidth > toolbarWidth * 0.5) {
        dropdown.append(customToolbars.last());
        $('.bs-toolbar-responsive').show();
    } else {
        toolbar.append($('.other-actions .btn-group').last());
        $('.bs-toolbar-responsive').hide();
    }**/
}

Pace.on('done', function() {
    responsiveToolBar();
});

$(window).on('resize', function () {
    responsiveToolBar();
});

$(document).ready(function() {
    $('[data-init-plugin=selectize]').each(function() {
        $(this).selectize({
            create: false,
            dropdownDirection: 'auto'
        });
        $('.selectize-dropdown-content').scrollbar();
    });

    // Initializes search overlay plugin.
    // Replace onSearchSubmit() and onKeyEnter() with
    // your logic to perform a search and display results
    $('[data-pages="search"]').searchPages({
        searchField: '#overlay-search',
        closeButton: '.overlay-close',
        suggestions: '#overlay-suggestions',
        brand: '.brand',
        onSearchSubmit: function(searchString) {
            $.ajax({
                type: "POST",
                async: true,
                url: "#",
                data: { value: searchString }
            }).done(function(content) {
                window.location.replace(content);
            });
        },
        onKeyEnter: function(searchString) {
            console.log("Live search for: " + searchString);
            var searchField = $('#overlay-search');
            var searchResults = $('.search-results');
            clearTimeout($.data(this, 'timer'));
            searchResults.fadeOut("fast");
            var wait = setTimeout(function() {
                searchResults.find('.result-name').each(function() {
                    if (searchField.val().length != 0) {
                        $(this).html(searchField.val());
                        searchResults.fadeIn("fast");
                    }
                });
            }, 500);
            $(this).data('timer', wait);
        }
    });
});

//USATO SOLO SUGLI ORDINI
$(document).on('click','button[data-ajax="true"]',function (e) {
    e.preventDefault();
    var button = $(this);
    if(button.attr('disable')== 'disable') return;
    button.attr('disable', 'disable');
    var controller = button.data('controller');
    var address = button.data('address') + '/' + controller;
    var method = button.data('method');
    var buttonClass = button.attr('class');
    button.addClass('fa fa-spinner fa-spin').fadeIn();


    $.ajax({
        type: method,
        url: address,
        data: {value: button.val() }
    }).done(function (content) {
        var done = button.data('fail');
        if(done != 'undefined'){
            var fn = window[done];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-check').css('background-color', 'green').fadeIn();
    }).fail(function (content) {
        var fail = button.data('fail');
        if(fail != 'undefined'){
            var fn = window[fail];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-times').css('background-color', 'red').fadeIn();
    }).always(function (content) {
        var always = button.data('always');
        if(always != 'undefined'){
            var fn = window[always];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        setTimeout(function () {
            button.removeClass().toggleClass(buttonClass);
            button.attr('disable', '');
        }, 2000);
    });
});

/** TODO: sostituire **/
$(document).on('submit','form[data-ajax="true"]',function (e) {
    e.preventDefault();
    var form = $(this);
    var controller = form.data('controller');
    var address = form.data('address') + '/' + controller;
    var method = form.attr('method');
    var button = $(form).find('[type="submit"]');
    var buttonSave = button.html();
    button.attr("disabled", "disabled");
    button.html('<i class="fa fa-spinner fa-spin"></i>').fadeIn();

    $.ajax({
        type: method,
        url: address,
        data: form.serialize()
    }).done(function (content) {
        var done = form.data('done');
        if(done != 'undefined'){
            var fn = window[done];
            if (typeof fn === "function") {
                fn.apply(null, [form]);
            }
        }
        button.fadeOut();
        button.html('<i class="fa fa-check"></i>').css('background-color', 'green').fadeIn();
    }).fail(function (content) {
        var fail = form.data('fail');
        if(fail != 'undefined'){
            var fn = window[fail];
            if (typeof fn === "function") {
                fn.apply(null, [form]);
            }
        }
        button.fadeOut();
        button.html('<i class="fa fa-times"></i>').css('background-color', 'red').fadeIn();
    }).always(function (content) {
        var always = form.data('always');
        if(always != 'undefined'){
            var fn = window[always];
            if (typeof fn === "function") {
                fn.apply(null, [form]);
            }
        }
        button.removeAttr("disabled");
        button.html(buttonSave).fadeIn(3);
        setTimeout(function () {
        }, 2000);
    });
});

// Smooth scroll for in page links
$(function(){
    var target, scroll;

    $("a[href*=#]:not([href=#])").on("click", function(e) {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            target = $(this.hash);
            target = target.length ? target : $("[id=" + this.hash.slice(1) + "]");

            if (target.length) {
                if (typeof document.body.style.transitionProperty === 'string') {
                    e.preventDefault();

                    var avail = $(document).height() - $(window).height();

                    scroll = target.offset().top;

                    if (scroll > avail) {
                        scroll = avail;
                    }

                    $("html").css({
                        "margin-top" : ( $(window).scrollTop() - scroll ) + "px",
                        "transition" : "1s ease-in-out"
                    }).data("transitioning", true);
                } else {
                    $("html, body").animate({
                        scrollTop: scroll
                    }, 1000);
                    return;
                }
            }
        }
    });

    $("html").on("transitionend webkitTransitionEnd msTransitionEnd oTransitionEnd", function (e) {
        if (e.target == e.currentTarget && $(this).data("transitioning") === true) {
            $(this).removeAttr("style").data("transitioning", false);
            $("html, body").scrollTop(scroll);
            return;
        }
    });
});

$(window).on('scroll',function(e) {
    var f = $('.followme');
    if (f.length != 0) {
        f.css('margin-top',($(window).scrollTop())+'px');
    }
});