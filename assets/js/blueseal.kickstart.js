function mq() {
    var toolbar = $('.bs-toolbar');
    var dropdown = $('.other-actions');
    var customToolbars = $('.bs-toolbar-custom');
    var toolbarCount = customToolbars.length;

    if ($.MatchMedia('(min-width:1276px)')) {

        var c = $('.other-actions .btn-group').length - 1;

        for (var i = c; i >= 0; i--) {
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

function responsiveToolBar() {
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

Pace.on('done', function () {
    responsiveToolBar();
});

$(window).on('resize', function () {
    responsiveToolBar();
});

$(document).ready(function () {
    $('[data-init-plugin=selectize]').each(function () {
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
        onSearchSubmit: function (searchString) {
            $.ajax({
                type: "POST",
                async: true,
                url: "#",
                data: {value: searchString}
            }).done(function (content) {
                window.location.replace(content);
            });
        },
        onKeyEnter: function (searchString) {
            console.log("Live search for: " + searchString);
            var searchField = $('#overlay-search');
            var searchResults = $('.search-results');
            clearTimeout($.data(this, 'timer'));
            searchResults.fadeOut("fast");
            var wait = setTimeout(function () {
                searchResults.find('.result-name').each(function () {
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
$(document).on('click', 'button[data-ajax="true"]', function (e) {
    e.preventDefault();
    var button = $(this);
    if (button.attr('disable') == 'disable') return;
    button.attr('disable', 'disable');
    var controller = button.data('controller');
    var address = button.data('address') + '/' + controller;
    var method = button.data('method');
    var buttonClass = button.attr('class');
    button.addClass('fa fa-spinner fa-spin').fadeIn();


    $.ajax({
        type: method,
        url: address,
        data: {value: button.val()}
    }).done(function (content) {
        var done = button.data('fail');
        if (done != 'undefined') {
            var fn = window[done];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-check').css('background-color', 'green').fadeIn();
    }).fail(function (content) {
        var fail = button.data('fail');
        if (fail != 'undefined') {
            var fn = window[fail];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-times').css('background-color', 'red').fadeIn();
    }).always(function (content) {
        var always = button.data('always');
        if (always != 'undefined') {
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
$(document).on('submit', 'form[data-ajax="true"]', function (e) {
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
        if (done != 'undefined') {
            var fn = window[done];
            if (typeof fn === "function") {
                fn.apply(null, [form]);
            }
        }
        button.fadeOut();
        button.html('<i class="fa fa-check"></i>').css('background-color', 'green').fadeIn();
    }).fail(function (content) {
        var fail = form.data('fail');
        if (fail != 'undefined') {
            var fn = window[fail];
            if (typeof fn === "function") {
                fn.apply(null, [form]);
            }
        }
        button.fadeOut();
        button.html('<i class="fa fa-times"></i>').css('background-color', 'red').fadeIn();
    }).always(function (content) {
        var always = form.data('always');
        if (always != 'undefined') {
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
$(function () {
    var target, scroll;

    $("a[href*=#]:not([href=#])").on("click", function (e) {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
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
                        "margin-top": ( $(window).scrollTop() - scroll ) + "px",
                        "transition": "1s ease-in-out"
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

$(window).on('scroll', function (e) {
    var f = $('.followme');
    if (f.length != 0) {
        f.css('margin-top', ($(window).scrollTop()) + 'px');
    }
});

$.fn.tagName = function () {
    return this.prop('tagName').toLowerCase();
};

getGet = function () {
    var self = this;
    this.all = {};

    var params = window.location.search.substr(1);
    params = params.split('&');

    params.forEach(function (v, k, arr) {
        var paramArr = v.split('=');
        self.all[paramArr[0]] = paramArr[1];
    });

    if (!Object.keys(this.all).length) {
        this.all = false;
    }

    this.get = function (param) {
        if (('object' == typeof param) && (param in self.all)) return self.all[param];
        else return false;
    };
};

$_GET = new getGet();

$.objMerge = function (...theArgs
)
{
    var argsLength = theArgs.length;
    if (2 > argsLength) throw "Almost 2 objects needed";
    $.each(theArgs, function (k, v) {
        if ('object' != typeof v) throw "Expected object, " + typeof v + " given";
    });

    var res = {};
    var countLoops = 0;
    for (var argName in theArgs) {
        for (var arrName in theArgs[argName]) {
            res[arrName] = theArgs[argName][arrName];
        }
    }
    return res;
}
;

/**
 *
 * @param params Object {id, ajaxUrl, done, ajaxMethod[, fail, always, ajaxdataType, ajaxMoreData]}
 * @returns {null}
 */
$.initFormByGetData = function (params) {
    if ('object' != typeof params) throw "params must to be contained into an object";
    if ('undefined' == typeof params.data) throw "an ID should to be passed";
    if ('undefined' == typeof params.ajaxUrl) throw "The 'ajaxAddress' parameter is mandatory";
    if ('undefined' == typeof params.done) throw "The 'doneCallback' parameter is mandatory";

    var def = {
        ajaxMethod: 'GET',
        ajaxdataType: 'json',
        ajaxExtraData: {},
        fail: function (res) {
            console.log(res)
        },
        always: function (res) {
            console.log(res)
        },
    };

    var opt = $.objMerge(def, params);
    delete def;
    if ('object' != typeof opt.data) opt.data = {id: opt.data};

    $.ajax({
        url: opt.ajaxUrl,
        method: opt.ajaxMethod,
        dataType: opt.ajaxdataType,
        data: opt.data
    }).done(function (res) {
            opt.done(res)
        }
    ).fail(function (res) {
        opt.fail(res);
    }).always(function (res) {
        opt.always(res);
    });
};

$.fn.fillTheForm = function (arrData) {
    var self = this;
    $.each(arrData, function (k, v) {
        var input = $(self).find('[name="' + k + '"]');
        if (input.length) {
            switch ($(input).tagName()) {
                case 'input':
                    $(input).val(v);
                    break;
                case 'select':
                    $(input).humanized('addItems', v);
                    break;
                case 'textarea':
                    $(input).html(v);
                    break;
                case 'radio':
                    $(input).find('[value="' + v + '"').prop('checked', true);
                    break;
                case 'checkbox':
                    $(input).prop('checked', true);
                    break;
            }
        }
    });


};

$.fn.humanized = function (method, data) {

    var selectObj = $(this).selectize()[0].selectize;
    var methods = {
        addItems: function (data) {
            if (Array.isArray(data)) {
                for (var opt in data) {
                    privateMethods.addSelectizeAllItem(data[opt]);
                }
            } else {
                privateMethods.addSelectizeAllItem(data);
            }
        }
    };

    var privateMethods = {
        addSelectizeAllItem: function (data) {
            if ('object' == typeof data) privateMethods.addSelectizeObjectItem(data);
            else privateMethods.addSelectizeRawItem(selectObj, data);
        },
        addSelectizeRawItem: function (selectObj, data) {
            var isOpt = false;
            for (var opt in selectObj.options) {
                if (data == selectObj.options[opt][selectObj.settings.valueField]) isOpt = true;
                break;
            }
            var addOpt = {};
            addOpt[selectObj.settings.valueField] = data;
            if (!isOpt) selectObj.addOption(addOpt);
            selectObj.addItem(data, true);
        },
        addSelectizeObjectItem: function (data) {
            if (selectObj.settings.valueField in data) {
                var isOpt = false;
                for (var opt in selectObj.options) {
                    if (data[selectObj.settings.valueField] == selectObj.options[opt][selectObj.settings.valueField]) isOpt = true;
                    break;
                }
                if (!isOpt) selectObj.addOption(data);
                selectObj.addItem(data[selectObj.settings.valueField], true);
            } else {
                throw "No object properties match with the Selectize valueField"
            }
        }
    };
    return methods[method](data);
};

$.bsModal = function (header, params) {
    //constructor
    var self = this;
    if ('undefined' == typeof header) {
        console.error("the param 'header' is mandatory");
        return false;
    }

    var opt = {
        body: '',
        isCancelButton: false,
        okLabel: 'Ok',
        cancelLabel: 'Cancel',
        cancelButtonEvent: function () {
            self.bsModal.modal('hide');
        },
        okButtonEvent: function () {
            self.bsModal.modal('hide');
        }
    };
    this.opt = $.objMerge(opt, params);
    delete opt;

    this.bsModal = $('#bsModal');
    this.header = $('#bsModal .modal-header h4');
    this.body = $('#bsModal .modal-body');
    this.cancelButton = $('#bsModal .modal-footer .btn-default');
    this.okButton = $('#bsModal .modal-footer .btn-success');

    this.header.html(header);
    this.body.html(this.opt.body);
    this.okButton.html(this.opt.okLabel).off().on('click', function (e) {
        e.preventDefault();
        self.opt.okButtonEvent()
    });

    this.cancelButton.html(this.opt.cancelLable);
    if (!opt.isCancelButton) this.cancelButton.hide();
    else {
        this.cancelButton.show();
        this.cancelButton.off().on('click', function (e) {
            e.preventDefault();
            self.opt.cancelButtonEvent()
        });
    }
    this.bsModal.modal();
    //constructor end

    this.writeHeader = function (header) {
        self.header.html(header);
    };

    this.writeBody = function (body) {
        self.body.html(body);
    };

    this.setOkEvent = function (callback) {
        self.okButton.off().on('click', callback);
    };

    this.setCancelEvent = function (callback) {
        self.cancelButton.off().on('click', callback);
    };

    this.setLabel = function (button /* ok, cancel */, string) {
        self[button + 'Button'].html(string);
    };
};

(function ($) {
    $.fn.selectDetails = function (value, type) {
        var prototypeId = 0;
        type = (type) ? type : '';
        value = (value) ? value : '';
        var self = this;
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {
                value: value,
                type: type,
                code: $('.product-code').html()
            }
        }).done(function ($content) {
            $(self).html($content);
            prototypeId = $(self).find(".detailContent").data('prototype-id');
            var productDataSheet = $(self).find(".Product_dataSheet");
            var selPDS = $(productDataSheet).selectize();
            selPDS[0].selectize.setValue(prototypeId, true);

            productDataSheet.on("change", function () {
                $(self).selectDetails($(this).find("option:selected").val(), 'change');
            });

            $(self).find(".productDetails select").each(function () {
                var sel = $(this).selectize({
                    valueField: 'id',
                    labelField: 'item',
                    searchField: ['item'],
                    options: window.detailsStorage
                });
                var initVal = $(this).data('init-selection');
                if (initVal != 'undefined' && initVal.length != 0) {
                    sel[0].selectize.setValue(initVal, true);
                } else {
                    sel[0].selectize.setValue(0, true);
                }
            });
        });
    }
})(jQuery);


(function ($) {
    /**
     *
     * @param defaultVal
     * @returns {boolean}
     */
    $.fn.isFieldValue = function (defaultVal, data, url) {
        var self = this;
        if (!url) url = '/blueseal/xhr/isFieldValue';
        if ('object' != typeof data) data = {};

        $(this).off().on('keyup', function () {
            if (($(this).val() != defaultVal) && ($(this).val().length)) {
                data[$(this).attr('name')] = $(this).val();
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: url,
                    data: data
                }).done(function (res) {
                    if (res) {
                        $(self).parent().addClass('has-error');
                        $(self).parents('form').bsForm('addError', 'isFieldFault');
                    }
                    else {
                        $(self).parent().removeClass('has-error');
                        $(self).parents('form').bsForm('removeError', 'isFieldFault');
                    }
                });
            }
        });
    }
})(jQuery);


(function ($) {
    /**
     *
     * @param primary hidden Field the input field used for checking if the edited entity is new or existent
     * @param url
     * @param data
     */
    $.fn.saveForm = function (primaryField, url, data) {
        if ('hidden' !== $(primaryField).attr('type')) throw "'primaryField' must be an hidden";

        if ($(primaryField).val()) {
            type = 'PUT';
        } else {
            type = 'POST';
        }
        $(document).ajaxForm({
                type: type,
                url: "#",
                formAutofill: true
            },
            new FormData(),
            function (res) {
                try {
                    res = JSON.parse(res);
                } catch (e) {
                    res = res;
                }
                if ('string' == typeof res) {
                    body.html(res);
                } else {
                    body.html(res['message']);
                    $('.product-code').html(res['code']['id'] + '-' + res['code']['productVariantId']);
                }
            }
        );
    };
})(jQuery);


(function($){
    $.fn.bsForm = function(method, params) {
        var self = this;

        var methods = {
            checkRequired: function() {
                var requiredFault = false;
                $(self).each(function(){
                    if ($(this).prop('required')) requiredFault = true;
                });
                if (requiredFault) {methods.addError('requiredFault')}
                else {methods.removeError('requiredFault')}
            },
            checkErrors: function() {
                if (!Array.isArray($(self).data('errors'))) $(self).data('errors', []);
                methods.checkRequired();
                /*var isFieldFault = false;
                $(self).find('input, select, textarea').each(function(){
                    console.log($(this).attr('name'));
                    if (1 == $(this).data('isFieldFault')) isFieldFault = true;
                });
                if (isFieldFault) {methods.addError('isFieldFault')}
                else {methods.removeError('isFieldFault')}*/
            },
            addError: function(err) {
                var arr = [];
                if (Array.isArray($(self).data('errors'))) var arr = $(self).data('errors');

                if (-1 == $.inArray(err, arr)) {
                    arr.push(err);
                    $(self).data('errors', arr);
                }
            },
            removeError: function(err) {
                var numb;
                var arr = [];
                if (Array.isArray($(self).data('errors'))) arr = $(self).data('errors');
                if ( -1 < (numb = $.inArray(err, arr))) {
                    arr.splice(numb, 1);
                    $(self).data('errors', arr);
                }
            },
            putOrPost: function() {
                var primaryField = $(self).data('primaryfield');
                if ($(primaryField).val().length) return 'PUT';
                else return 'POST';
            },
            save: function(url) {
                methods.checkErrors();
                if ($(self).data('errors').length) {
                    //display Errors
                    var errs = $(self).data('errors');
                    var errorMsg = '';
                    for (var i in errs) {
                        if ('undefined' != messages.errors[errs[i]]) errorMsg+= messages.errors[errs[i]] + '<br />';
                    }
                    modal = new $.bsModal(
                        'OOPS!',
                        {
                            body: errorMsg
                        }
                    );
                } else {
                    //save
                    var opt = {
                        contentType: 'multipart/form-data',
                        processData: false
                    };  
                    
                    opt['method'] = methods.putOrPost();
                    opt['url'] = url;
                    opt['data'] = new FormData(document.querySelector('#' + $(self).attr('id')));

                    var formDataObject = new FormData();

                    $(self).find('input:not([type=file],[type=radio],[type=checkbox]), textarea, select').each(function () {
                        if (typeof $(this).attr('name') == 'undefined') return;
                        formDataObject.append($(this).attr('name'), $(this).val());
                    });

                    var radioNames = [];
                    $(self).find('input[type=radio]').each(function () {
                        if (typeof $(this).attr('name') == 'undefined') return;
                        radioNames.push($(this).attr('name'));
                    });
                    var unique = radioNames.filter(function (value, index, self) {
                        return self.indexOf(value) === index;
                    });
                    unique.forEach(function (element, index, array) {
                        formDataObject.append(element, $('[name=' + element + ']:checked').val());
                    });

                    $(self).find('input[type=checkbox]:checked').each(function () {
                        if (typeof $(this).attr('name') == 'undefined') return;
                        formDataObject.append($(this).attr('name'), $(this).val());
                    });

                    $(self).find(':file').each(function () {
                        if (typeof this.name == 'undefined') return;
                        if (this.files.length == 0) return;
                        formDataObject.append(this.name, this.files[0]);
                    });

                    opt['data'] = formDataObject;
                    
                    $.ajax(opt).done(function(res){
                        console.log(res);
                    });
                }
            }
        };

        var privateMethods = {

        };

        var messages = {
            errors: {
                isFieldFault: 'Uno o più valori inseriti sono già presenti nel nostro sistema e devono essere modificati',
                requiredFault: 'Uno o più campi obbligatori sono stati omessi'
            }
        };

        return methods[method](params);
    }
})(jQuery);