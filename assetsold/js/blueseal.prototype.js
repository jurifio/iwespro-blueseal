String.prototype.monkeyReplaceAll = function (search, replacement) {
    let target = this;
    return target.replace( (new RegExp(search, 'g')) , replacement === null || replacement.length === 0 ? "" : replacement);
};

Number.prototype.toDecimal = function() {
    return parseFloat(+Math.round(this.toString() + "e+2")  + "e-2");
};

function b64DecodeUnicode(str) {
    return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}

function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}

window.bsToolbarLastButtonId = 0;

let ui = function () {
    this.registry = [];
};

ui.prototype.register = function (widget) {
    if (widget instanceof Widget) {
        this.registry.push(widget)
    } else {
        throw new Error('The widget you are trying to register must be an instance of Widget');
    }
};

ui.prototype.unregister = function (widget) {
    if (widget instanceof Widget) {
        let idx = this.registry.indexOf(widget);
        if (idx > -1) {
            this.registry.splice(widget, 1);
        } else {
            throw new Error('The widget you are trying to unregister does not exist');
        }
    } else {
        throw new Error('The widget you are trying to register must be an instance of Widget');
    }
};

let Widget = function () {
};

let UiElement = function (uiElementTagObject, allowedConfigKeyArray) {
    Widget.call(this);
    this.uiElementTagObject = uiElementTagObject;
    this.allowedConfigKeyArray = allowedConfigKeyArray;
    this.data = null;
};
UiElement.prototype = Object.create(Widget.prototype);
UiElement.prototype.constructor = UiElement;

UiElement.prototype.parseConfig = function () {

};

/** override getscript to create a debuggable script */
jQuery.extend({
    getScript: function (url, callback) {
        let head = document.getElementsByTagName("head")[0];
        let script = document.createElement("script");
        script.src = url;

        // Handle Script loading
        {
            let done = false;

            // Attach handlers for all browsers
            script.onload = script.onreadystatechange = function () {
                if (!done && (!this.readyState ||
                    this.readyState == "loaded" || this.readyState == "complete")) {
                    done = true;
                    if (callback)
                        callback();

                    // Handle memory leak in IE
                    script.onload = script.onreadystatechange = null;
                }
            };
        }

        head.appendChild(script);

        // We handle everything using the script element injection
        return this;
    },
});

/**
 * @constructor
 */
let DffBooleanAjax = function () {
};

/**
 * @param $ajaxConfig
 * @returns {*}
 */
DffBooleanAjax.prototype.getDeferred = function ($ajaxConfig) {

    let dff = $.Deferred();
    let ajaxPromise = $.ajax($ajaxConfig);

    ajaxPromise.then(function (data) {
        let result = Boolean(data);
        dff.resolve(result);
    }, function () {
        dff.reject();
    });

    return dff.promise();
};

/**
 * @param data
 * @param expected
 * @returns {ButtonCfg}
 * @constructor
 */
let ButtonCfg = function (data, expected) {
    if (!(this instanceof ButtonCfg)) {
        return new ButtonCfg();
    }
    this.expected = expected;
    this.data = this.extractFrom(data);
};
ButtonCfg.prototype = Object.create(UiElement.prototype);
ButtonCfg.prototype.constructor = UiElement;

/**
 * @param data
 * @returns {Array}
 */
ButtonCfg.prototype.extractFrom = function (data) {

    let that = this;
    let tempData = {};
    let theData = data;

    that.expected.forEach(function (v) {
        if (typeof theData[v] != 'undefined') tempData[v] = theData[v];
    });

    return tempData;
};

/**
 * @param data
 * @returns {Button}
 * @constructor
 */
let Button = function (data) {

    if (!(this instanceof Button)) {
        return new Button(data);
    }

    if (typeof data != 'object') {
        throw new Error('Button(data) argument must be an object literal')
    }

    window.bsToolbarLastButtonId++;

    this.id = window.bsToolbarLastButtonId;
    this.template = "<{tag} {attributes} {id}>{icon}</{tag}>";

    this.cfg = new ButtonCfg(data, ['tag', 'icon', 'loadEvent', 'permission', 'event', 'button']);
    this.tagAttr = new ButtonCfg(data, ['class', 'rel', 'href', 'title', 'name', 'download', 'target', 'data-target']);
    this.dataAttr = new ButtonCfg(data, ['placement', 'toggle', 'json']);

    this.permissionCheckerEndPoint = "/blueseal/xhr/CheckPermission";
};

/**
 * @type {DffBooleanAjax}
 */
Button.prototype = Object.create(DffBooleanAjax.prototype);

/**
 * @returns {*}
 */
Button.prototype.getHref = function () {
    return this.tagAttr.data.href;
};

/**
 * @returns {*}
 */
Button.prototype.getTitle = function () {
    return this.tagAttr.data.title;
};

/**
 * @param template
 */
Button.prototype.setTemplate = function (template) {
    if (template.indexOf('{tag}') < 0 || template.indexOf('{attributes}') < 0 || template.indexOf('{id}') < 0) {
        throw new Error('Invalid tag template. Must contain at least {tag}, {attributes} and {id} placeholders');
    }
    this.template = template;
};

/**
 * @param element
 */
Button.prototype.draw = function (element) {
    let that = this;
    let html = this.parse();

    if (element.prop('tagName') == 'BS-BUTTON') {
        $(document).trigger('bs.draw.inpage.button', [element, this, html])
    } else {
        $(document).trigger('bs.draw.toolbar.button', [element, this, html])
    }

    $('#bsButton_' + this.id).on('click', function (e) {
        if (that.cfg.data.event) {
            $(this).trigger(that.cfg.data.event, [$(this), that, e]);
        }
    });
};

/**
 * @returns {string}
 */
Button.prototype.parse = function () {

    let tag = this.template.replace(/\{tag}/gi, this.cfg.data.tag);

    if (this.template.indexOf('{icon}') >= 0) {
        let icons = this.cfg.data.icon.split(' ');

        if (icons.length == 2) {
            this.cfg.data.icon = '<span class="fa-stack"><i class="fa ' + icons[1] + ' fa-stack-1x"></i><i class="fa ' + icons[0] + ' fa-stack-2x"></i></span>';
        } else {
            this.cfg.data.icon = '<i class="fa ' + this.cfg.data.icon + '"></i>';
        }

        tag = tag.replace(/\{icon}/gi, this.cfg.data.icon);
    }

    let attributes = "";

    $.each(this.dataAttr.data, function (k, v) {
        attributes += 'data-' + k + '="' + v + '" ';
    });

    $.each(this.tagAttr.data, function (k, v) {
        attributes += k + '="' + v + '" ';
    });

    tag = tag.replace(/\{attributes}/gi, attributes);

    tag = tag.replace(/\{id}/gi, 'id="bsButton_' + this.id + '"');

    return tag;
};

/**
 * @returns {*}
 */
Button.prototype.checkPermission = function () {

    let that = this;

    let theButton = $('#bsButton_' + that.id);

    if (window.localStorage.getItem(that.cfg.data.permission) == '1') {
        if (theButton.is('select')) {
            theButton.prop('disabled', false).attr('disabled', false);
            theButton.next().prop('disabled', false).attr('disabled', false);
        } else {
            theButton.prop('disabled', false).attr('disabled', false);
        }
        return;
    } else if (window.localStorage.getItem(that.cfg.data.permission) == '0') {
        theButton.on('click', function (e) {
            e.preventDefault();
        });
        theButton.hide();
    }

    this.getDeferred({
        url: this.permissionCheckerEndPoint,
        data: {permission: this.cfg.data.permission},
        context: this,
        type: 'GET'
    }).done(function (response) {
        if (response == '1') {
            window.localStorage.setItem(that.cfg.data.permission, '1');

            if (theButton.is('select')) {
                theButton.prop('disabled', false).attr('disabled', false);
                theButton.next().prop('disabled', false).attr('disabled', false);
            } else {
                theButton.prop('disabled', false).attr('disabled', false);
            }
        } else {
            window.localStorage.setItem(that.cfg.data.permission, '0');
            theButton.on('click', function (e) {
                e.preventDefault();
            });
            theButton.hide();
        }
    });
};

const checkPermission = function(permission, cache = true) {

    let deferred = $.Deferred();
    let timer = setInterval(function () {
        deferred.notify();
    }, 100);

    setTimeout(function () {
        clearInterval(timer);
        if (cache === true && 'undefined' !== typeof window.localStorage.getItem(permission) && window.localStorage.getItem(permission) !== null) {
            if(window.localStorage.getItem(permission) == 1) deferred.resolve();
            else deferred.reject();
        } else {
            $.ajax({
                url: "/blueseal/xhr/CheckPermission",
                data: {
                    "permission": permission
                }
            }).done(function(res) {
                window.localStorage.setItem(permission,res);
                if(res == 1) deferred.resolve();
                deferred.reject(res);

            }).fail(function(res) {
                window.localStorage.setItem(permission,0);
                deferred.reject(res);
            });
        }
    }, 300);

    /** restituisco il deferred */
    return deferred.promise();
}

/**
 * @param data
 * @constructor
 */
let Select = function (data) {
    Button.call(this, data);
    this.options = data.options;
    this.template = "<{tag} {attributes} {id}>{options}</{tag}>";
};
/**
 * @type {Button}
 */
Select.prototype = Object.create(Button.prototype);

/**
 * @param element
 */
Select.prototype.draw = function (element) {
    let that = this;
    let html = this.parse();

    if (element.prop('tagName') == 'BS-BUTTON') {
        $(document).trigger('bs.draw.inpage.button', [element, this, html])
    } else {
        $(document).trigger('bs.draw.toolbar.button', [element, this, html])
    }

    let theButton = $('#bsButton_' + this.id);

    if (that.cfg.data.button == 'true') {
        theButton.next().on('click', function (e) {
            if (that.cfg.data.event) {
                $(this).trigger(that.cfg.data.event, [$(this).prev(), that, e]);
            }
        });
    } else {
        theButton.on('change', function (e) {
            if (that.cfg.data.event) {
                $(this).trigger(that.cfg.data.event, [$(this), that, e]);
            }
        });
    }
};

/**
 * @returns {string}
 */
Select.prototype.parse = function () {
    let options = "";

    let selectTag = Button.prototype.parse.call(this);
    let selectedOption = 'undefined';

    if (typeof this.options.selected != 'undefined') {
        selectedOption = this.options.selected;
        delete this.options.selected;
    }

    $.each(this.options, function (k, v) {
        if (k == selectedOption) {
            options += '<option selected="selected" value="' + k + '">' + v + '</option>';
        } else {
            options += '<option value="' + k + '">' + v + '</option>';
        }
    });

    selectTag = selectTag.replace(/\{options}/gi, options);

    return selectTag;
};

/**
 * @param data
 * @constructor
 */
let ButtonToggle = function (data) {
    Button.call(this, data);

    this.on = data.on;
    this.key = data.key;
    this.stateController = (function ($, key) {
        let dt = $('table[data-datatable-name]').DataTable();
        return dt.ajax.params()[key]
    })(jQuery, this.key);
    this.template = "<{tag} {attributes} {id}>{icon}</{tag}>";
};

/**
 * @type {Button}
 */
ButtonToggle.prototype = Object.create(Button.prototype);

/**
 * @returns {string}
 */
ButtonToggle.prototype.parse = function () {
    let buttonToggleTag = Button.prototype.parse.call(this);
    let css = '';

    if (typeof this.stateController !== 'undefined') {
        css = this.on;
    }

    buttonToggleTag = buttonToggleTag.replace(/(class=")(btn [a-z-]+)(")/i, '$1$2 ' + css + '$3');

    return buttonToggleTag;
};

/**
 * @param config
 * @returns {Alert}
 * @constructor
 */
let Alert = function (config) {
    if (!(this instanceof Alert)) {
        return new Alert(config);
    }

    this.wrapper = $('.bs.alertbox.wrapper');
    this.alertBox = $('.bs.alertbox .content');
    this.data = $.extend({
        type: "warning",
        message: "undefined",
        dismissable: true,
        selfClose: true,
        loader: false,
        closeTimerMs: 5000,
        silent: true,
        audio: {
            warning: '/assets/audio/alert.mp3',
            danger: '/assets/audio/alert.mp3',
            info: '/assets/audio/alert.mp3',
            success: '/assets/audio/alert.mp3'
        },
        icon: {
            warning: "fa-warning",
            danger: "fa-times-circle",
            info: "fa-info-circle",
            success: "fa-thumbs-o-up"
        }
    }, config);

    return this.draw();
};

/**
 * @returns {Alert}
 */
Alert.prototype.draw = function () {
    let that = this;

    if ($.inArray(that.data.type, ['warning', 'danger', 'info', 'success']) < 0) {
        throw new Error('Invalid alert type. Allowed types are [warning;danger;info;success]')
    }

    that.alertBox
        .removeClass('alert-warning')
        .removeClass('alert-danger')
        .removeClass('alert-info')
        .removeClass('alert-success')
        .addClass('alert-' + that.data.type);

    that.alertBox.find('i').eq(0)
        .removeClass()
        .addClass('fa ' + that.data.icon[that.data.type] + ' big-icon');


    if (that.data.loader === true) {
        that.alertBox.find('i').eq(0).hide();

        that.alertBox.find('p').eq(0)
            .html('<img src="/assets/img/bsloader.svg" width="32" />&nbsp;<span>' + that.data.message + '</span>');
    } else {
        that.alertBox.find('p').eq(0)
            .html('<span>' + that.data.message + '</span>');
    }

    if (that.data.dismissable === true) {
        that.alertBox.find('.dismiss').on('click', function () {
            that.close();
        });
    } else {
        that.alertBox.find('.dismiss').hide();
    }

    return that;
};

Alert.prototype.open = function () {
    let that = this;
    if (!that.data.silent) {
        let sfx = new Audio(that.data.audio[that.data.type]);
        sfx.play();
    }
    that.alertBox.off();
    that.wrapper.css('height', '8%');
    that.alertBox.addClass('opened');

    if (that.data.selfClose == true) {
        setTimeout(function () {
            that.close();
        }, that.data.closeTimerMs);
    }
};

Alert.prototype.close = function () {
    let that = this;

    that.alertBox.one('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function () {
        that.wrapper.css('height', '0%');
    });

    that.alertBox.removeClass('opened');
};

/**
 * @param $element
 * @param counterOffset
 * @returns {Replica}
 * @constructor
 */
let Replica = function ($element, counterOffset) {

    if (!$element instanceof jQuery) {
        throw new Error('$element must be an instance of jQuery');
    }

    if (!this instanceof Replica) {
        return new Replica($element)
    }

    this.counter = (typeof counterOffset != 'undefined') ? counterOffset : 0;
    this.$replica = $element.clone(true, true);
    this.$lastReplica = null;
    $element.hide();
};

/**
 * @param $appendToThisElement
 * @param numberOfCopies
 */
Replica.prototype.replicateInto = function ($appendToThisElement, numberOfCopies) {

    let that = this;

    if (!!numberOfCopies === false) {
        numberOfCopies = 1;
    }

    for (let i = 0; i < numberOfCopies; i++) {
        let $e = that.loopThrough(that.$replica.clone(true, true));

        $e.addClass('fade');

        $appendToThisElement.append($e);

        setTimeout(function ($e) {
            $('html, body').animate({
                scrollTop: $e.offset().top
            }, 1000);
            $.drawUI();
            $e.addClass('in');
        }($e), 500);

        that.counter++;
    }

    $.each($('bs-rcounter'), function () {
        $(this).replaceWith(that.counter);
    });

    that.$lastReplica = $e;

    return $e;
};

/**
 * @param $element
 * @returns {*}
 */
Replica.prototype.loopThrough = function ($element) {

    let that = this;

    $element.each(function () {

        if (typeof $(this).attr('id') != 'undefined') {
            $(this).attr('id', $(this).attr('id') + '_' + $.randomString(8));
        }

        if (typeof $(this).attr('name') != 'undefined') {
            $(this).attr('name', $(this).attr('name') + '_' + $.randomString(8));
        }

        that.loopThrough($(this).children());
    });

    return $element;
};

/**
 * @returns {*}
 */
Replica.prototype.getLastReplicaId = function () {

    let that = this;

    return that.$lastReplica.attr('id');
};

/**
 * @returns {Echo}
 * @constructor
 */
let Echo = function () {

    if (!this instanceof Echo) {
        return new Echo()
    }

    this.bound = false;
};

/**
 * @param $source
 * @param $target
 * @param eventsArray
 */
Echo.prototype.bind = function ($source, $target, eventsArray) {

    if (!eventsArray instanceof Array) {
        throw new Error('eventsArray must be an array containing events\' names triggering echo');
    }

    let that = this;

    if (that.bound) {
        throw new Error('Echo already bound, to unbind use Echo::unbind()');
    }

    that.$source = $source;
    that.$target = $target;
    that.events = eventsArray.join(' ');

    that.$source.on(that.events, function (e) {
        that.$target.vv(that.$source.vv());
    });

    that.bound = true;
};

Echo.prototype.unbind = function () {

    let that = this;

    that.$source.off();
};

/**
 * @returns {Modal}
 * @constructor
 */
let Modal = function () {

    if (!this instanceof Modal) {
        return new Modal()
    }

    this.modal = $('#bsModal');
    this.header = $('#bsModal .modal-header h4');
    this.body = $('#bsModal .modal-body');
    this.cancelButton = $('#bsModal .modal-footer .btn-default');
    this.okButton = $('#bsModal .modal-footer .btn-success');
};

/**
 * @param title
 */
Modal.prototype.setTitle = function (title) {
    this.header.html(title);
};

/**
 * @param text
 */
Modal.prototype.setOkButton = function (text) {
    this.okButton.html(text);
};

/**
 * @param text
 */
Modal.prototype.setCancelButton = function (text) {
    this.cancelButton.html(text);
};

/**
 * @param content
 */
Modal.prototype.setContent = function (content) {
    this.body.html(content);
};

Modal.prototype.appendContent = function (content) {
    this.body.append(content);
};

Modal.prototype.prependContent = function (content) {
    this.body.prepend(content);
};

Modal.prototype.hide = function () {
    this.okButton.off();
    this.body.html('');
    this.modal.modal('hide');
};

Modal.prototype.show = function () {
    this.modal.modal('show');
};

/**
 * @param dzConf
 * @returns {Modal}
 */
Modal.prototype.addDropZone = function (dzConf) {

    this.setContent('' +
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">' +
        '<div class="fallback">' +
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>');

    this.dz = new Dropzone('#dropzoneModal', dzConf);

    return this;
};

Modal.prototype.attachDropZoneEvent = function (event, callback) {
    this.dz.on(event, callback);
};

Modal.prototype.detachDropZoneEvent = function (event) {
    this.dz.off(event);
};

/**
 * @param newvalue
 * @returns {*|jQuery}
 */
$.fn.vv = function (newvalue) {
    if (!!newvalue === false) {
        if (typeof $(this).prop('value') !== 'undefined') {
            if ($(this).is('select')) {
                return $(this).text();
            } else {
                return $(this).val();
            }
        } else {
            return $(this).html();
        }
    } else {
        if (typeof $(this).prop('value') !== 'undefined') {
            $(this).val(newvalue);
        } else {
            $(this).html(newvalue);
        }
    }
};

/**
 * @param l
 * @returns {string}
 */
$.randomString = function (l) {
    return Math.round((Math.pow(36, l + 1) - Math.random() * Math.pow(36, l))).toString(36).slice(1);
};

$.drawUI = function () {

    let buttons = $('bs-button');

    $.each(buttons, function () {
        let button = new Button($(this).data());
        button.draw($(this));
    });

    $('[data-init-plugin=selectize]').each(function () {
        $(this).selectize({
            create: false,
            dropdownDirection: 'auto'
        });
        $('.selectize-dropdown-content').scrollbar();
    });

    $(function () {
        $('.color-picker').colorpicker();
    });

    $('textarea.summer').summernote({
        lang: "it-IT",
        height: 100
    });
};

let Portlet = function (data) {
    if (!(this instanceof Portlet)) {
        return new Portlet();
    }

    this.controller = new ButtonCfg(data, ['controller', 'url']);
    this.params = new ButtonCfg(data, ['params']);
    this.cfg = new ButtonCfg(data, ['tag', 'icon', 'permission', 'event', 'button']);
    this.tagAttr = new ButtonCfg(data, ['class', 'rel', 'href', 'title', 'name', 'download']);
    this.dataAttr = new ButtonCfg(data, ['placement', 'toggle', 'target', 'json']);

    this.permissionCheckerEndPoint = "/blueseal/xhr/CheckPermission";

    if (this.controller.data.url == 'undefined') {
        this.controller.data.url = '/blueseal/xhr';
    }

    this.ajaxPromise = $.ajax({
        url: this.controller.data.url + '/' + this.controller.data.controller,
        method: "GET",
        data: this.params.data.params
    }).promise();

    this.loadingTemplate = "Loading";
    this.failTemplate = "Failed Loading";

};

Portlet.prototype = Object.create(UiElement.prototype);
Portlet.prototype.constructor = UiElement;

Portlet.prototype.draw = function (that) {
    let _this = this;

    _this.ajaxPromise.progress(function () {
        $(that).replaceWith(_this.loadingTemplate)
    }).done(function (result) {
        $(that).replaceWith(result);
    }).fail(function () {
        $(that).replaceWith(_this.failTemplate);
    });
};

$.MatchMedia = function (a) {
    return window.styleMedia.matchMedium(a);
};

$.QueryString = (function (a) {
    if (a == "") return {};
    let b = {};
    for (let i = 0; i < a.length; ++i) {
        let p = a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));

$.decodeGetStringFromUrl = function (url) {
    "use strict";
    if(typeof url === 'undefined') url = window.location.href;
    let getString = url.split('\?', 2);
    if (getString.length == 0) return false;
    if (getString.length == 1) return {baseUrl: url};
    if (getString.length == 2) return $.extend({baseUrl: getString[0]}, $.decodeGetString(getString[1]));
};

/**
 * @deprecated
 * @param url
 * @returns {*}
 */
$.myDecodeGetStringFromUrl = function (url) {
    "use strict";
    if ('undefined' === typeof url) url = window.location.href;
    let getString = url.split('\?', 2);
    let ret = {};
    ret.url = '';
    ret.params = {};
    if (getString.length == 0) return false;
    if (getString.length == 1) ret.url = getString[0];
    if (getString.length == 2) {
        ret.url = getString[0];
        ret.params = $.decodeGetString(getString[1]);
    }
    return ret;
};

$.decodeGetString = function (a) {
    "use strict";
    if (a == "") return {};
    a = a.split('&');
    let b = {};
    for (let i = 0; i < a.length; ++i) {
        let p = a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
};

$.encodeGetString = function (o) {
    "use strict";
    let a = [];
    let r;
    if (typeof o.baseUrl != 'undefined' && o.baseUrl != 'undefined') {
        r = o.baseUrl;
        delete o.baseUrl;
    }
    $.each(o, function (k, v) {
        if (k == 'baseUrl') return;
        a.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
    });
    return r + '?' + a.join('&');
};

/**
 * @deprecated
 * @param o
 * @returns {string}
 */
$.myEncodeGetString = function (o) {
    "use strict";
    let a = [];
    let r;
    if (typeof o.url != 'undefined' && o.url != 'undefined') {
        r = o.url;
        delete o.url;
    }
    $.each(o.params, function (k, v) {
        a.push(k + "=" + v);
    });
    return r + '?' + a.join('&');
};

$.addGetParam = function (url, field, val) {
    "use strict";
    let c = $.decodeGetStringFromUrl(url);
    c[field] = val;
    return $.encodeGetString(c);
};

Date.prototype.toDateTimeString = function () {
    return this.toISOString().slice(0, 10) + ' ' + this.toISOString().slice(11, 19);
};