/**
 * @param a
 * @param b
 * @param inclusive
 * @returns {boolean}
 */
Number.prototype.between = function (a, b, inclusive) {
    var min = Math.min(a, b),
        max = Math.max(a, b);

    return inclusive ? this >= min && this <= max : this > min && this < max;
};

$.fn.selectText = function () {
    var doc = document
        , element = this[0]
        , range, selection
        ;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};

$.ajaxForm = function (ajaxConf, formDataObject) {

    var dff = $.Deferred();
    var conf = $.extend({}, {
        contentType: 'multipart/form-data',
        processData: false
    }, ajaxConf);

    if (conf.formAutofill && conf.formAutofill == true) {
        var errors = [];
        var formSelector = conf.formSelector || 'document';
        $('input:not([type=file]), textarea, select').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            if ($(this).attr('required') == 'required' && $(this).val().length === 0) {
                errors.push($(this).attr('name'));
            }
        });

        if (errors.length) {
            //TODO GESTIRE MEGLIO QUESTA COSA
            return dff.reject();
        }

        $('input:not([type=file],[type=radio],[type=checkbox]), textarea, select').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            formDataObject.append($(this).attr('name'), $(this).val());
        });

        var radioNames = [];
        $('input[type=radio]').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            radioNames.push($(this).attr('name'));
        });
        var unique = radioNames.filter(function (value, index, self) {
            return self.indexOf(value) === index;
        });
        unique.forEach(function (element, index, array) {
            formDataObject.append(element, $('[name=' + element + ']:checked').val());
        });

        $('input[type=checkbox]:checked').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            formDataObject.append($(this).attr('name'), $(this).val());
        });

        $(':file').each(function () {
            if (typeof this.name == 'undefined') return;
            if (this.files.length == 0) return;
            formDataObject.append(this.name, this.files[0]);
        });
    }

    conf.data = formDataObject;

    var promise = $.ajax(conf);

    promise.then(function (data) {
        dff.resolve(data);
    }, function (data) {
        dff.reject(data.responseText);
    });

    return dff.promise();
};

$.fn.ajaxForm = function (ajaxConf, callback) {
    var me = this;
    var conf = $.extend({}, {
        contentType: 'multipart/form-data',
        processData: false
    }, ajaxConf);

    var errorMsg = '';
    var formSelector = conf.formSelector || 'document';

    if ('undefined' != typeof conf.customParsingForm) {
        var customErr = conf.customParsingForm(me);
        if ('string' == typeof customErr) {
            errorMsg+= customErr + '<br />';
        }
    }

    var requiredErr = [];
    $(me).find('input:not([type=file]), textarea, select').each(function () {
        var isParsable = true;
        if ('undefined' == typeof $(this).attr('name')) isParsable = false;
        if ('undefined' != typeof $(this).attr('id')) {
            if (-1 < $(this).attr('id').indexOf('-selectized')) isParsable = false;
        }
        if (true === isParsable) {
            if (typeof $(this).attr('name') == 'undefined') return;
            if (($(this).attr('required') == 'required') || ($(this).hasClass('required'))) {
                if ($(this).val().length === 0) {
                    requiredErr.push($(this).attr('name'));
                }
            }
        }
    });

    if (requiredErr.length) {
        errorMsg+= 'I seguenti campi sono obbligatori e non sono stati compilati:<br />';
        $.each(requiredErr, function (k, v) {
            var label = $('label[for="' + v + '"]');
            if (0 == label.length) label = $('label[for="' + v + '-selectized"]');
            errorMsg += label.html() + '<br />';
        });
    }
    if ('' != errorMsg) {
        callback(errorMsg);
    } else {
        var formDataObject = new FormData();

        $(me).find('input:not([type=file],[type=radio],[type=checkbox]), textarea, select').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            formDataObject.append($(this).attr('name'), $(this).val());
        });

        var radioNames = [];
        $(me).find('input[type=radio]').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            radioNames.push($(this).attr('name'));
        });
        var unique = radioNames.filter(function (value, index, self) {
            return self.indexOf(value) === index;
        });
        unique.forEach(function (element, index, array) {
            formDataObject.append(element, $('[name=' + element + ']:checked').val());
        });

        $(me).find('input[type=checkbox]:checked').each(function () {
            if (typeof $(this).attr('name') == 'undefined') return;
            formDataObject.append($(this).attr('name'), $(this).val());
        });

        $(me).find(':file').each(function () {
            if (typeof this.name == 'undefined') return;
            if (this.files.length == 0) return;
            formDataObject.append(this.name, this.files[0]);
        });

        conf.data = formDataObject;

        $.ajax(conf).always(function (res) {
            callback(res);
        });
    }
};

(function ($) {

    $(document).ready(function () {

        var toolbar = $('bs-toolbar');
        var operations = $("div.bs-toolbar");

        $.each($(toolbar).children('bs-toolbar-group'), function () {

            operations.append('<div class="dt-buttons btn-group bs-toolbar-custom"><div class="btn-group-label">' + $(this).data('group-label') + '</div></div>');
            var group = $("div.bs-toolbar .dt-buttons");
            $.each($(this).children('bs-toolbar-button,bs-toolbar-select,bs-toolbar-button-toggle'), function () {
                var _this = $(this);
                var data = $(this).data();
                /** per recupero configurazioni pulzante */
                var deferred = $.Deferred();

                /** genero placeholder con id randomico */
                var randId = 'bs' + Math.ceil(Math.random() * (100000 - 1) + 1);
                var tag = $('<' + _this.prop('tagName') + ' id="' + randId + '" ></' + _this.prop('tagName') + '>');
                group.last().append(tag);
                var placeHolder = $('#' + randId);

                /** recupero impostazioni */
                timer = setInterval(function () {
                    deferred.notify();
                }, 100);

                setTimeout(function () {
                    clearInterval(timer);
                    if ('undefined' != typeof data.remote) {
                        $.getScript("/assets/" + data.remote + ".js", function (res) {
                                $.extend(data, data, window.buttonSetup);
                                delete window.buttonSetup;
                                deferred.resolve()
                            }
                        );
                    } else {
                        deferred.resolve();
                    }
                }, 300);

                /** quando ho finito sostituisco il placeholder con il pulsante */
                deferred.done(function () {
                    var element;
                    switch (_this.prop('tagName').toLowerCase()) {
                        case 'bs-toolbar-button': {
                            element = new Button(data);
                            element.draw(placeHolder);
                            break;
                        }
                        case 'bs-toolbar-select': {
                            element = new Select(data);
                            element.draw(placeHolder);
                            break;
                        }
                        case 'bs-toolbar-button-toggle': {
                            element = new ButtonToggle(data);
                            element.draw(placeHolder);
                            break;
                        }
                    }
                    element.checkPermission();
                    $(document).trigger('bs.toolbar.element.drawn', element);
                    if (typeof element.cfg.data.loadEvent != 'undefined') {
                        $(document).trigger(element.cfg.data.loadEvent, element);
                    }
                });
            });
        });

        operations.append('<div class="dt-buttons btn-group bs-toolbar-responsive"><div class="btn-group-label">&nbsp;</div></div>');
        operations.children('.btn-group').last().append('<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Altro <span class="caret"></span></a><div class="dropdown-menu other-actions"></div>');
        $('.btn-group-label').next().css('border-radius', '2px');

        $.fn.tooltip && $('[data-toggle="tooltip"], [rel="tooltip"]').tooltip({
            container: 'body',
            delay: {"show": 500, "hide": 100}
        });

        $('.color-picker').each(function(k,v) {
            v.colorpicker();
        });

        $('.toolbar-definition').remove();
    });

    $(document).on('bs.draw.inpage.button', function (e, button, o, html) {
        $(button).replaceWith(html);
    });

    $(document).on('bs.draw.toolbar.button', function (e, container, button, html) {
        $(container).replaceWith(html);
        $(button).prop('disabled', true).attr('disabled', true);
        $('.btn-group-label').next().css('border-radius', '2px');
    });

    $(document).ready(function () {
        var portlet = $('bs-portlet');
        $.each($(portlet), function () {
            var port = new Portlet($(this).data());
            port.draw(this);
        });
    });

     $(document).ready(function() {
        $(document).on('click', '.enlarge-your-img', function(e){
            var tagName = $(e.target).prop('tagName');
            var src = ('IMG' == tagName) ? $(e.target).attr('src') : $(e.target).children('img').attr('src');
            modal = new $.bsModal(
                'Immagine Prodotto',
                {
                    body: '<img style="max-width: 100%" src="' + src + '" />',
                    okButtonLabel: 'Chiudi'
                }
            );

        });
     });

})(jQuery);