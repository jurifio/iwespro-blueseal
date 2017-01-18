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
        if ('string' === typeof content) {
            modal = $.bsModal('Cambio dello stato dell\'ordine', {
                body: content
            });
        }
        button.fadeOut();
        button.html('<i class="fa fa-times"></i>').css('background-color', 'red').fadeIn();
        if ('object' === typeof content) {
            modal = $.bsModal('Cambio dello stato dell\'ordine', {
                body: content.responseText
            });
        }
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

    /*$('a[href*=#]:not([href=#])').on("click", function (e) {
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
     });*/

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

    if ("" != params[0]) {
        params.forEach(function (v, k, arr) {
            var paramArr = v.split('=');
            self.all[paramArr[0]] = paramArr[1];
        });
    }

    if (!Object.keys(this.all).length) {
        this.all = false;
    }

    this.get = function (param) {
        if (('string' == typeof param) && (false != self.all)) {
            if (param in self.all) {
                return self.all[param];
            } else {
                return false;
            }
        }
        else return false;
    };
};

$_GET = new getGet();


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

    var opt = $.extend(def, params);
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
                if (data == selectObj.options[opt][selectObj.settings.valueField]) {
                    isOpt = true;
                    break;
                }
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
    var self = this;
    if ('undefined' != typeof modal) {
        modal.hide();
        delete(modal);
    }
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
        cancelLabel: 'Annulla',
        cancelButtonEvent: function () {
            self.hide();
        },
        okButtonEvent: function () {
            self.hide();
        }
    };
    this.opt = $.extend(opt, params);
    delete opt;

    this.bsModal = $('#bsModal');
    this.header = $('#bsModal .modal-header h4');
    this.body = $('#bsModal .modal-body');
    this.cancelButton = $('#bsModal .modal-footer .btn-default');
    this.okButton = $('#bsModal .modal-footer .btn-success');
    this.cross = $('#bsModal button.close');

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

    this.cross.off().on('click', function () {
        self.okButton.prop('disabled', false);
        self.bsModal.modal('hide');
    });
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

    this.showCancelBtn = function () {
        self.cancelButton.show();
    };

    this.hideCancelBtn = function () {
        self.cancelButton.hide();
    };

    this.setCancelEvent = function (callback) {
        self.cancelButton.off().on('click', callback);
    };

    this.setLabel = function (button /* ok, cancel */, string) {
        self[button + 'Button'].html(string);
    };

    this.disableOkButton = function () {
        self.okButton.prop('disabled', true);
    };

    this.enableOkButton = function () {
        self.okButton.prop('disabled', false);
    };

    this.show = function () {
        self.bsModal.modal();
    };

    this.hide = function () {
        self.body.html('');
        self.bsModal.modal('hide');
    };
};

(function ($) {
    $.fn.selectDetails = function (value, type, opt) {
        var self = this;
        var def = {
            productCode: '',
            getDetails: false,
            after: function (self) {
            }
        };
        var opt = $.extend({}, def, opt);
        if ($('.product-code').html()) opt.productCode = $('.product-code').html();
        var prototypeId = 0;
        type = (type) ? type : '';
        value = (value) ? value : '';

        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {
                value: value,
                type: type,
                code: opt.productCode
            }
        }).done(function (content) {
            $(self).html(content);
            prototypeId = $(self).find(".detailContent").data('prototype-id');
            var productDataSheet = $(self).find(".Product_dataSheet");
            var selPDS = $(productDataSheet).selectize();
            selPDS[0].selectize.setValue(prototypeId, true);

            productDataSheet.on("change", function () {
                $(self).selectDetails($(this).find("option:selected").val(), 'change');
            });

            var detailsOptions = [];
            $.ajax({
                url: '/blueseal/xhr/DetailGetAll',
                method: 'GET'
            }).done(function (res) {
                detailsOptions = JSON.parse(res);
                $(self).find(".productDetails select").each(function () {
                    var sel = $(this).selectize({
                        valueField: 'id',
                        labelField: 'item',
                        searchField: ['item'],
                        options: detailsOptions
                    });
                    var initVal = $(this).data('init-selection');
                    if (initVal != 'undefined' && initVal.length != 0) {
                        sel[0].selectize.setValue(initVal, true);
                    } else {
                        sel[0].selectize.setValue(0, true);
                    }
                });

                var selectName = $('#ProductName_1_name').selectize();
                var pName = $('.detailContent').data('productName');
                selectName[0].selectize.addOption({name: pName});
                selectName[0].selectize.addItem(pName);
                selectName[0].selectize.refreshOptions();
                selectName[0].selectize.setValue(pName, true);
                opt.after(self);
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

    $.fn.bsForm = function (method, params) {
        var self = this;

        //impedisco la sovrapposizione di chiamate ajax
        if ('undefined' == typeof bsformSaving) bsformSaving = 0;
        var methods = {
            checkRequired: function () {
                var requiredFault = false;
                $(self).find('select, input').each(function () {
                    if (($(this).prop('required')) && ('' == $(this).val())) requiredFault = true;
                });
                if (requiredFault) {
                    methods.addError('requiredFault')
                }
                else {
                    methods.removeError('requiredFault')
                }
            },
            checkErrors: function () {
                if (!Array.isArray($(self).data('errors'))) $(self).data('errors', []);
                methods.checkRequired();
                var isFieldFault = false;
                $(self).find('input, select, textarea').each(function () {
                    console.log($(this).attr('name'));
                    if (1 == $(this).data('isFieldFault')) isFieldFault = true;
                });
                if (isFieldFault) {
                    methods.addError('isFieldFault')
                }
                else {
                    methods.removeError('isFieldFault')
                }
            },
            addError: function (err) {
                var arr = [];
                if (Array.isArray($(self).data('errors'))) var arr = $(self).data('errors');

                if (-1 == $.inArray(err, arr)) {
                    arr.push(err);
                    $(self).data('errors', arr);
                }
            },
            removeError: function (err) {
                var numb;
                var arr = [];
                if (Array.isArray($(self).data('errors'))) arr = $(self).data('errors');
                if (-1 < (numb = $.inArray(err, arr))) {
                    arr.splice(numb, 1);
                    $(self).data('errors', arr);
                }
            },
            putOrPost: function () {
                var primaryField = $(self).data('primaryfield');
                if ('undefined' != typeof primaryField) {
                    if ($(primaryField).length) {
                        if ($(primaryField).val().length) return 'PUT';
                    }
                }
                return 'POST';
            },
            save: function (params) {
                if (0 == bsformSaving) {
                    bsformSaving = 1;
                    var opt = {
                        //preferenze esecuzione metodo
                        dataType: 'JSON',
                        excludeFields: [],
                        excludeEmptyFields: true,
                        //parametri chiamata ajax
                        url: '#',
                        contentType: 'multipart/form-data',
                        processData: true,
                        method: methods.putOrPost(),
                        onCheckError: function (errorMsg) {
                            modal = new $.bsModal(
                                'OOPS!',
                                {
                                    body: errorMsg
                                }
                            );
                        },
                        onFail: function (res) {
                            console.log(res)
                        },
                        doAlways: function (res) {
                        }

                    };
                    opt = $.extend(opt, params);
                    if ('string' != typeof params['url']) throw 'the "url" parameter is mandatory and it must to be string type';
                    if ('function' != typeof params['onDone']) throw 'the "url" parameter is mandatori and it must to be a callback';
                    methods.checkErrors();
                    if ($(self).data('errors').length) {
                        //display Errors
                        var errs = $(self).data('errors');
                        var errorMsg = '';
                        for (var i in errs) {
                            if ('undefined' != messages.errors[errs[i]]) errorMsg += messages.errors[errs[i]] + '<br />';
                        }
                        opt.onCheckError(errorMsg);
                        bsformSaving = 0;
                    } else {

                        //save
                        opt = $.extend(opt, params);
                        var data = {};
                        //var formDataObject = new FormData();

                        $(self).find('input:not([type=file],[type=radio],[type=checkbox]), textarea, select').each(function () {
                            if ('undefined' != typeof $(this).attr('name')) {
                                //controllo le condizioni impostate nelle opzioni del metodo
                                if ((-1 == $.inArray($(this).attr('name'), opt['excludeFields'])) &&
                                    ( ("" != $(this).val()) || (false === opt['excludeEmptyFields']) ) //if is required, empty values don't will be used
                                ) {
                                    data[$(this).attr('name')] = $(this).val();
                                    //formDataObject.append($(this).attr('name'), $(this).val());
                                }
                            }
                        });

                        var radioNames = [];
                        $(self).find('input[type=radio]').each(function () {
                            if (typeof $(this).attr('name') == 'undefined') throw 'Non possono esistere campi senza l\'attributo "name"';

                            //controllo le condizioni impostate nelle opzioni del metodo
                            if ((-1 == $.inArray($(this).attr('name'), opt['excludeFields'])) &&
                                ( ("" != $(this).val()) && (true === opt['excludeEmptyFields']) ) //if is required, empty values don't will be used
                            ) {
                                radioNames.push($(this).attr('name'));
                            }
                        });
                        var unique = radioNames.filter(function (value, index, self) {
                            return self.indexOf(value) === index;
                        });
                        unique.forEach(function (element, index, array) {
                            data[$(element).attr('name')] = $('[name=' + element + ']:checked').val();
                            //formDataObject.append(element, $('[name=' + element + ']:checked').val());
                        });

                        $(self).find('input[type=checkbox]:checked').each(function () {
                            if (typeof $(this).attr('name') == 'undefined') return;
                            data[$(this).attr('name')] = $(this).val();
                            //formDataObject.append($(this).attr('name'), $(this).val());
                        });

                        $(self).find(':file').each(function () {
                            if (typeof this.name == 'undefined') return;
                            if (this.files.length == 0) return;
                            formDataObject.append(this.name, this.files[0]);
                        });

                        opt['data'] = data;
                        //opt['data'] = formDataObject;

                        $.ajax({
                            url: opt['url'],
                            data: opt['data'],
                            method: opt['method'],
                            dataType: opt['dataType']
                            //contentType: opt['contentType']
//                        processData: opt['processData']
                        }).done(function (res) {
                            opt.onDone(res, opt['method']);
                        }).fail(function (res) {
                            opt.onFail(res, opt['method']);
                        }).always(function (res) {
                            opt.doAlways(res, opt['method']);
                            bsformSaving = 0;
                        });
                    }
                }
            }
        };

        var privateMethods = {};

        var messages = {
            errors: {
                isFieldFault: 'Uno o più valori inseriti sono già presenti nel nostro sistema e devono essere modificati',
                requiredFault: 'Uno o più campi obbligatori sono stati omessi'
            }
        };

        return methods[method](params);
    }
})(jQuery);

(function ($) {
    $.fn.bsCatalog = function (params) {
        var self = this;
        // Se non ci sono i parametri si assume che il l'interfaccia sia già esistente
        if ('undefined' == typeof params) {
            var initParams = $(this).data('initParams');
            if ('undefined' == typeof initParams) throw "No params given";
            return $.bsCatalog(this, initParams);

            //inizializzazione di una nuova interfaccia
        } else {
            //controllo che l'interfaccia sia già stata scaricata una volta, altrimenti la riscarico
            var catalogTemplate = $('.catalog-template');
            if (catalogTemplate.length) {
                return $.bsCatalog(this, params, catalogTemplate.html());
            }
            else {
                $.ajax({
                    url: '/blueseal/xhr/CatalogGetTemplateController',
                    method: 'get'
                }).done(function (res) {
                    $('body').append($('<div class="catalog-template" style="display: none">' + res + '</div>'));
                    return $.bsCatalog(self, params, $('.catalog-template').html());
                });
            }
        }
    };

    $.bsCatalog = function (elem, params, template) {

        var self = this;
        //template parts
        this.movementLineTemplate = false;
        this.productTemplate = false;

        //DOM elements
        this.dom = elem;
        this.form = false;
        this.container = false;
        this.submitButton = false;
        this.searchBlock = false;
        this.submitBlock = false;
        this.movementDate = false;
        this.products = {};
        this.defaults = {
            searchfield: true,
            search: false,
            mode: 'multi'
        };
        this.opt = {};

        this.searchProduct = function (search, callback) {
            if (!search.length) return false;
            var shop = '';
            var shopSelect = $('.mag-shop');
            if (shopSelect.length) shop = shopSelect.val();
            $.ajax({
                url: '/blueseal/xhr/CatalogController',
                method: 'get',
                dataType: 'json',
                data: {search: search, shop: shop}
            }).done(function (res) {
                if (false == res) {
                    self.submitwarning(['Il prodotto cercato non esiste. Controlla l\'esattezza del codice inserito']);
                } else {
                    if ('string' === typeof res) self.submitError([], [res]);
                    else {
                        self.form.find('.alert').css('opacity', '0');
                        callback(res);
                    }
                }
            }).fail(function (res) {
                console.error(res);
            });
        };

        this.addProduct = function (product) {
            var productList = self.productList;
            if ('single' == this.opt.mode) productList.html('');
            if ('multi' == this.opt.mode) //TODO aggiungi pulsante per chiudere il singolo prodotto;
                var prodTemp = self.productTemplate.clone();
            if ('single' == this.opt.mode) prodTemp.find('.product-close').remove();
            var prodId = 'product-' + product.id + '-' + product.productVariantId;

            //controllo se la scheda del prodotto è già presente
            var actualProd = productList.find('#' + prodId);
            if (actualProd.length) {
                self.editMoves(product, actualProd.find('table'));
            } else {
                prodTemp.attr('id', 'product-' + product.id + '-' + product.productVariantId);
                var prodTitle = prodTemp.find('.product-title');
                var title = product.id + '-' + product.productVariantId + ' / ' + product.itemno + ' # ' + product.productVariantName;
                title += ' <span class="small">costo: ' + product.value + ' - prezzo vendita: ' + product.price + '</span>';
                prodTitle.html(title);
                var table = prodTemp.find('table');
                var head = $(table).find('thead');
                var body = $(table).find('tbody');
                var sizes = self.writeSizesTable(product);
                head.append(sizes.head);
                body.append(sizes.stock);
                body.append(sizes.moves);
                prodTemp.data('product', product);
                var prodList = self.form.find('.mag-product-list');
                prodList.append(prodTemp);
                var prod = prodList.find('#' + prodId);
                var closeProd = prod.find('.product-close');
                closeProd.on('click', function (e) {
                    e.preventDefault();
                    prod.remove();
                    if (!prodList.find('.mag-product').length) {
                        self.submitBlock.css('display', 'none');
                    }
                });

                body.find('.move-qty').each(function () {
                    //controllo sui movimenti, mai minori della disponibilità
                    $(this).off().on('change', function (e) {
                        var qty = $(this).data('stock');
                        var move = parseInt($(this).val());
                        if (0 > qty + move) $(this).val(qty * -1);
                        //controllo il segno dei movimenti
                        var sign = self.getCauseSign();
                        if ('+' == sign) {
                            if (0 == $(this).val().indexOf('-')) {
                                $(this).val('0');
                            }
                        } else if ('-' == sign) {
                            if (-1 == $(this).val().indexOf('-')) {
                                //$(this).val('-' + $(this).val());
                                $(this).val('0');
                            }
                        } else if (false == sign) {
                            $(this).val('0');
                        }
                    });
                });

                self.submitBlock.css('display', 'block');
                self.movementDate.css('display', 'block');
            }
            return prod;
        };

        this.writeSizesTable = function (product) {
            var head = $('<tr class="sizes"></tr>');
            var stock = $('<tr class="stocks"></tr>');
            var moves = $('<tr class="moves"></tr>');

            head.append($('<th>Misure</th>'));
            stock.append($('<th>Disponibilità</th>'));
            moves.append($('<th>Movimenti</th>'));
            for (var i in product.sizes) {
                var qt = '';
                var paddingStyle = '';
                head.append($('<th>' + product.sizes[i] + '</th>'));
                if ('undefined' != typeof product.sku[i]) {
                    qt = (('undefined' != typeof product.sku[i]) && (0 < product.sku[i]['qty'])) ? product.sku[i]['qty'] : '';
                    paddingStyle = (product.sku[i]['padding']) ? 'style="color: red"' : '';
                }
                stock.append($('<td ' + paddingStyle + '>' + qt + '</td>'));
                var moveQt = ('undefined' != typeof product.moves[i]) ? product.moves[i] : '';
                var fieldName = product.id + '-' + product.productVariantId + '-' + i;
                moves.append($('<td><input type="number" data-stock="' + (('' != qt) ? qt : 0) + '" class="move-qty form-control" name="move-' + fieldName + '" value="' + moveQt + '"></td>'));
            }
            return {head: head, stock: stock, moves: moves};
        };

        this.editMoves = function (product, table) {
            for (var i in product.moves) {
                var fieldName = product.id + '-' + product.productVariantId + '-' + i;
                var field = $(table).find('input[name="move-' + fieldName + '"]');
                field.val(parseInt(field.val()) + parseInt(product.moves[i]));
            }
        };

        this.getCauseSign = function () {
            var causeElem = $('.mag-movementCause option:selected');
            var sign = false;
            if (-1 < causeElem.html().indexOf('(+)') || -1 < causeElem.html().indexOf('(-)')) {
                var pos = causeElem.html().indexOf('(') + 1;
                sign = causeElem.html().substr(pos, 1);
            }
            return sign;
        };


        this.submitError = function (domElems, errors) {
            var f = self.form;
            f.find('input, select').each(function () {
                $(this).parent().removeClass('hasError');
            });

            var alert = f.find('.alert');
            alert.css('visibility', 'visible');
            alert.css('opacity', '1');
            alert.removeClass('alert-success');
            alert.addClass('alert-danger');
            alert.html('');
            for (var msg in errors) {
                alert.append(errors[msg] + '<br />');
            }
            alert.css('visibility', 'visible');

            setTimeout(function () {
                alert.animate({'opacity': '0'}, 'fast', function () {

                });
            }, 8000);
        };

        this.submitSuccess = function (msg) {
            var f = self.form;

            var alert = f.find('.alert');
            alert.css('visibility', 'visible');
            alert.css('opacity', '1');
            alert.removeClass('alert-danger');
            alert.removeClass('alert-warning');
            alert.addClass('alert-success');
            alert.html('');
            for (var i in msg) {
                alert.append(msg[i] + '<br />');
            }
            alert.css('visibility', 'visible');

            setTimeout(function () {
                alert.animate({'opacity': '0'}, 'fast', function () {
                });
            }, 8000);
        };

        this.submitwarning = function (msg) {
            var f = self.form;

            var alert = f.find('.alert');
            alert.css('visibility', 'visible');
            alert.css('opacity', '1');
            alert.removeClass('alert-danger');
            alert.removeClass('alert-success');
            alert.addClass('alert-warning');
            alert.html('');
            for (var i in msg) {
                alert.append(msg[i] + '<br />');
            }
            alert.css('visibility', 'visible');

            setTimeout(function () {
                alert.animate({'opacity': '0'}, 'fast', function () {
                });
            }, 8000);
        };

        this.assignMovementLimit = function (operator) {
            self.form.find('.move-qty').each(function () {
                self.qtyDynamicValidation(this, operator);
                this.off().on('change keyup', function (e) {
                    self.qtyDynamicValidation(this, operator);
                })
            });
        };

        this.qtyDynamicValidation = function (elem, op) {
            if (('+' == operator) && (0 > $(elem).val())) {
                //$(elem).val('0');
            } else if (('-' == operator) && ( 0 < $(elem).val()) && ('-' != $(elem).val())) {
                //$(elem).val('0');
            }
        };

        this.save = function (successCallback, failCallback) {
            var f = self.form;
            $('#form-movement').bsForm('save', {
                url: '/blueseal/xhr/CatalogController',
                method: 'post',
                excludeFields: ['search-item'],
                dataType: 'json',
                excludeEmptyFields: true,
                onCheckError: function (msg) {
                    self.submitError([], [msg]);
                },
                onDone: function (res, method) {
                    if (true == res) {
                        self.submitSuccess(['Il movimento è stato caricato correttamente']);
                        self.productList.html('');
                        self.submitBlock.css('display', 'none');
                    } else {
                        self.submitError([], [res]);
                    }
                },
            });
            var post = {};
        };

        //constructor

        //preparo tutti i template separati in blocchi
        //this.movementLineTemplate = $(template).find('.mag-movementLine').clone();
        this.productTemplate = $(template).find('.mag-product').clone();
        //this.productTemplate.find('.mag-movements').html('');

        //faccio un merge dei parametri raccolti sopra i dati di default
        this.opt = $.extend(this.defaults, params);

        //scrivo e inizializzo i blocchi
        this.form = $(template).clone();
        this.productList = this.form.find('.mag-product-list').html('');
        this.container = this.productList.parent();
        this.form.data('initParams', this.opt);
        this.searchBlock = this.form.find('.mag-searchBlock');
        this.movementDate = this.form.find('.mag-movementDate');
        //this.movementDate.css('display', 'none');
        this.submitBlock = this.form.find('.mag-submit');
        this.submitBlock.css('display', 'none');

        if (true != this.opt.searchField) this.searchBlock.css('display', 'none');

        //creo il form se non esiste
        if (!$(this.dom).find('form').length) $(this.dom).append($(this.form));

        //faccio partire i selectize

        var shopSelect = $('.mag-shop');

        if ('undefined' != typeof this.opt.product) {
            if ('string' == typeof this.opt.product) {
                var string = this.opt.product;
                this.searchProduct(string, function (res) {
                    self.addProduct(res);
                });
            } else if ($.isArray(this.opt.product)) {
                for (var i in this.opt.product) {
                    var string = this.opt.product[i];
                    this.searchProduct(string, function (res) {
                        self.addProduct(res);
                    });
                }
            }
        }


        //evento ricerca
        var searchBtn = this.searchBlock.find('.search-btn');
        searchBtn.on('click', function (e) {
            e.preventDefault();
            var string = self.form.find('.search-item').val();
            self.searchProduct(string, function (res) {
                self.addProduct(res);
            });
            //$('.search-item').selectize()[0].selectize.setValue('', true);
        });

        //ricerca per barcode
        /*        var searchInput = this.searchBlock.find('.search-item');
         searchInput.on('keypress', function(e){
         if (13 == e.charCode) {
         e.preventDefault();
         $(this).select();
         searchBtn.trigger('click');
         }
         });*/

        //selectize search field
        var searchInput = this.searchBlock.find('.search-item');
        searchInput.selectize({
            valueField: 'code',
            labelField: 'code',
            searchField: 'code',
            options: [],
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div><span class="small">codice: </span><strong>' + escape(item.code) + '</strong><br /><span class="small">CPF e Variante: </span><strong>' + escape(item.cpfVar) + '</strong></span></div>';
                }
            },
            load: function (query, callback) {
                if (2 > query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/GetProductByAnyString',
                    type: 'GET',
                    data: {
                        search: query
                    },
                    dataType: 'json',
                    error: function () {
                        callback();
                    },
                    success: function (res) {
                        callback(res);
                    }
                });
            }
        });

        this.submitBlock.find('.mag-submit-btn').on('click', function (e) {
            e.preventDefault();
            self.save();
        });
        this.submitBlock.find('.mag-return-on-top').on('click', function (e) {
            e.preventDefault();
            $("html, body").animate({scrollTop: 0}, "fast");
            $('.search-item').selectize()[0].selectize.focus();
            $('.mag-searchBlock input').focus();
        });

        //azzero le quantità che non rispettano i criteri
        var selectCause = this.form.find('.mag-movementCause');
        selectCause.on('change', function () {
            var moves = self.form.find('.move-qty');
            var sign = self.getCauseSign();
            if (false == sign) {
                moves.each(function () {
                    if ('' != $(this).val())
                        $(this).val(0);
                });
            } else if ('+' == sign) {
                moves.each(function () {
                    if (0 > $(this).val()) {
                        $(this).val(0);
                    }
                });
            } else if ('-' == sign) {
                moves.each(function () {
                    if (0 < $(this).val()) {
                        $(this).val(0);
                    }
                });
            }
        });
        //end constructor
    };
})(jQuery);

$(document).on('keypress', '.inputPrice', function (e) {
    console.log(e);
    var target = e.target;
    e.preventDefault();
    var permitted = "1234567890,.";
    var char = String.fromCharCode(e.which);
    var val = $(this).val();
    if (-1 < permitted.indexOf(char)) {
        if ((-1 == val.indexOf(',')) || (2 >= val.length - val.indexOf(',')) || ((2 < val.length - val.indexOf(',')) && (target.selectionStart <= val.indexOf(',')))) {
            char = ('.' == char) ? ',' : char;
            if (char == ',') {
                if (-1 == val.indexOf(',')) {
                    if (0 == val.length) $(this).val('0,');
                    else if (target.selectionStart >= val.length - 2) {
                        var pos = target.selectionStart;
                        var before = val.substring(0, target.selectionStart);
                        var after = val.substring(target.selectionStart);
                        $(this).val(before + char + after);
                        $(this).setCursorPosition(pos + 1);
                    }
                }
            } else {
                var pos = target.selectionStart;
                var before = val.substring(0, target.selectionStart);
                var after = val.substring(target.selectionStart);
                $(this).val(before + char + after);
                $(this).setCursorPosition(pos + 1);
            }
        }
    }
});

$.fn.setCursorPosition = function (pos) {
    this.each(function (index, elem) {
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    });
    return this;
};

$.getTemplate = function (templateName) {
    return $.get({
        url: "/blueseal/xhr/TemplateFetchController",
        data: {
            templateName: templateName
        }
    })
};