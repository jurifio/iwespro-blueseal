window.bsToolbarLastButtonId = 0;

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

jQuery.fn.selectText = function(){
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

(function($) {

    $.MatchMedia = function(a) {
        return window.styleMedia.matchMedium(a);
    };

    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'));

    $.ajaxForm = function(ajaxConf, formDataObject) {

        var dff = $.Deferred();
        var conf = $.extend({},{
            contentType: 'multipart/form-data',
            processData: false
        },ajaxConf);

        if (conf.formAutofill && conf.formAutofill == true) {
            $('input:not([type=file],[type=radio]), textarea, select').each(function() {
                if(typeof $(this).attr('name') == 'undefined') return;
                formDataObject.append($(this).attr('name'), $(this).val());
            });

            var radioNames = [];
            $('input[type=radio]').each(function() {
                radioNames.push($(this).attr('name'));
            });
            var unique = radioNames.filter(function(value,index,self) {
                return self.indexOf(value) === index;
            });
            unique.forEach(function(element,index,array) {
                formDataObject.append(element, $('[name='+element+']:checked').val());
            });

            $(':file').each(function() {
                if(typeof this.name == 'undefined') return;
                formDataObject.append(this.name,this.files[0]);
            });
        }

        conf.data = formDataObject;

        var promise = $.ajax(conf);

        promise.then(function(data) {
            dff.resolve(data);
        }, function(data) {
            dff.reject(data.responseText);
        });

        return dff.promise();
    };

})(jQuery);

/**
 * @constructor
 */
var DffBooleanAjax = function () {};

/**
 * @param $ajaxConfig
 * @returns {*}
 */
DffBooleanAjax.prototype.getDeferred = function ($ajaxConfig) {

    var dff = $.Deferred();
    var ajaxPromise = $.ajax($ajaxConfig);

    ajaxPromise.then(function (data) {
        var result = Boolean(data);
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
var ButtonCfg = function (data, expected) {
    if (!(this instanceof ButtonCfg)) {
        return new ButtonCfg();
    }
    this.expected = expected;
    this.data = this.extractFrom(data);
};

/**
 * @param data
 * @returns {Array}
 */
ButtonCfg.prototype.extractFrom = function (data) {

    var that = this;
    var tempData = {};
    var theData = data;

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
var Button = function (data) {

    if (!(this instanceof Button)) {
        return new Button(data);
    }

    if (typeof data != 'object') {
        throw new Error('Button(data) argument must be an object literal')
    }

    window.bsToolbarLastButtonId++;

    this.id = window.bsToolbarLastButtonId;
    this.template = "<{tag} {attributes} {id}>{icon}</{tag}>";

    this.cfg = new ButtonCfg(data, ['tag', 'icon', 'permission', 'event', 'button']);
    this.tagAttr = new ButtonCfg(data, ['class', 'rel', 'href', 'title', 'name', 'download']);
    this.dataAttr = new ButtonCfg(data, ['placement', 'toggle', 'target', 'json']);

    this.permissionCheckerEndPoint = "/blueseal/xhr/CheckPermission";

    var that = this;

    this.getHref = function () {
        return this.tagAttr.data.href;
    };

    this.getTitle = function () {
        return this.tagAttr.data.title;
    };

    this.setTemplate = function (template) {
        if (template.indexOf('{tag}') < 0 || template.indexOf('{attributes}') < 0 || template.indexOf('{id}') < 0) {
            throw new Error('Invalid tag template. Must contain at least {tag}, {attributes} and {id} placeholders');
        }
        this.template = template;
    };

    this.draw = function (element) {

        var html = that.parse();

        if (element.prop('tagName') == 'BS-BUTTON') {
            $(document).trigger('bs.draw.inpage.button', [element, that, html])
        } else{
            $(document).trigger('bs.draw.toolbar.button', [element, that, html])
        }

        var theButton = $('#bsButton_' + that.id);

        if (theButton.is('select')) {
            theButton.next().on('click', function (e) {
                if (that.cfg.data.event) {
                    $(this).trigger(that.cfg.data.event, [$(this).prev(), that, e]);
                }
            });
        } else {
            theButton.on('click', function (e) {
                if (that.cfg.data.event) {
                    $(this).trigger(that.cfg.data.event, [$(this), that, e]);
                }
            });
        }
    };
};

/**
 * @type {DffBooleanAjax}
 */
Button.prototype = Object.create(DffBooleanAjax.prototype);

/**
 * @returns {string}
 */
Button.prototype.parse = function () {

    var tag = this.template.replace(/\{tag}/gi, this.cfg.data.tag);

    if (this.template.indexOf('{icon}') >= 0) {
        var icons = this.cfg.data.icon.split(' ');

        if (icons.length == 2) {
            this.cfg.data.icon = '<span class="fa-stack"><i class="fa ' + icons[1] + ' fa-stack-1x"></i><i class="fa ' + icons[0] + ' fa-stack-2x"></i></span>';
        } else {
            this.cfg.data.icon = '<i class="fa ' + this.cfg.data.icon + '"></i>';
        }

        tag = tag.replace(/\{icon}/gi, this.cfg.data.icon);
    }

    var attributes = "";

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

    var that = this;

    var theButton = $('#bsButton_' + that.id);

    if (window.localStorage.getItem(that.cfg.data.permission) == '1') {
        if (theButton.is('select')) {
            theButton.prop('disabled', false).attr('disabled', false);
            theButton.next().prop('disabled', false).attr('disabled', false);
        } else {
            theButton.prop('disabled', false).attr('disabled', false);
        }
        return;
    }

    this.getDeferred({
        url: this.permissionCheckerEndPoint,
        data: {permission: this.cfg.data.permission},
        context: this,
        type: 'GET'
    }).done(function (response) {
        if (response == true) {
            window.localStorage.setItem(that.cfg.data.permission,'1');

            if (theButton.is('select')) {
                theButton.prop('disabled', false).attr('disabled', false);
                theButton.next().prop('disabled', false).attr('disabled', false);
            } else {
                theButton.prop('disabled', false).attr('disabled', false);
            }
        } else {
            window.localStorage.setItem(that.cfg.data.permission,'0');
            theButton.on('click', function (e) {
                e.preventDefault();
            });
        }
    });
};

/**
 * @param data
 * @constructor
 */
var Select = function(data)
{
    Button.call(this, data);

    this.options = data.options;
    this.template = "<{tag} {attributes} {id}>{options}</{tag}><a {attributes}>{icon}</a>";
};

/**
 * @type {Button}
 */
Select.prototype = Object.create(Button.prototype);

/**
 * @returns {string}
 */
Select.prototype.parse = function ()
{
    var options = "";

    if (this.cfg.data.button == false) {
        this.setTemplate("<{tag} {attributes} {id}>{options}</{tag}>");
    }

    var selectTag  = Button.prototype.parse.call(this);
    var selectedOption = 'undefined';

    if (typeof this.options.selected != 'undefined') {
        selectedOption = this.options.selected;
        delete this.options.selected;
    }

    $.each(this.options, function (k, v) {
        if (k == selectedOption) {
            options += '<option selected="selected" value="'+k+'">'+v+'</option>';
        } else {
            options += '<option value="'+k+'">'+v+'</option>';
        }
    });

    selectTag = selectTag.replace(/\{options}/gi, options);

    return selectTag;
};

/**
 * @param config
 * @returns {Alert}
 * @constructor
 */
var Alert = function(config)
{
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
    },config);

    return this.draw();
};

/**
 * @returns {Alert}
 */
Alert.prototype.draw = function()
{
    var that = this;

    if ($.inArray(that.data.type,['warning','danger','info','success']) < 0) {
        throw new Error('Invalid alert type. Allowed types are [warning;danger;info;success]')
    }

    that.alertBox
        .removeClass('alert-warning')
        .removeClass('alert-danger')
        .removeClass('alert-info')
        .removeClass('alert-success')
        .addClass('alert-'+that.data.type);

    that.alertBox.find('i').eq(0)
        .removeClass()
        .addClass('fa '+that.data.icon[that.data.type]+' big-icon');


    if (that.data.loader === true) {
        that.alertBox.find('i').eq(0).hide();

        that.alertBox.find('p').eq(0)
            .html('<img src="/assets/img/bsloader.svg" width="32" />&nbsp;<span>'+that.data.message+'</span>');
    } else {
        that.alertBox.find('p').eq(0)
            .html('<span>'+that.data.message+'</span>');
    }

    if (that.data.dismissable === true) {
        that.alertBox.find('.dismiss').on('click', function() {
            that.close();
        });
    } else {
        that.alertBox.find('.dismiss').hide();
    }

    return that;
};

Alert.prototype.open = function()
{
    var that = this;

    var sfx = new Audio(that.data.audio[that.data.type]);
    sfx.play();

    that.alertBox.off();
    that.wrapper.css('height','8%');
    that.alertBox.addClass('opened');

    if (that.data.selfClose == true) {
        setTimeout(function() {
            that.close();
        },that.data.closeTimerMs);
    }
};

Alert.prototype.close = function()
{
    var that = this;

    that.alertBox.one('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function() {
        that.wrapper.css('height','0%');
    });

    that.alertBox.removeClass('opened');
};

/**
 * @param $element
 * @param counterOffset
 * @returns {Replica}
 * @constructor
 */
var Replica = function($element, counterOffset) {

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
Replica.prototype.replicateInto = function($appendToThisElement, numberOfCopies) {

    var that = this;

    if (!!numberOfCopies === false) {
        numberOfCopies = 1;
    }

    for (var i=0;i<numberOfCopies;i++) {
        var $e = that.loopThrough(that.$replica.clone(true, true));

        $e.addClass('fade');

        $appendToThisElement.append($e);

        setTimeout(function($e) {
            $('html, body').animate({
                scrollTop: $e.offset().top
            }, 1000);
            $.drawUI();
            $e.addClass('in');
        }($e), 500);

        that.counter++;
    }

    $.each($('bs-rcounter'), function() {
        $(this).replaceWith(that.counter);
    });

    that.$lastReplica = $e;

    return $e;
};

/**
 * @param $element
 * @returns {*}
 */
Replica.prototype.loopThrough = function($element) {

    var that = this;

    $element.each(function() {

        if (typeof $(this).attr('id') != 'undefined') {
            $(this).attr('id',$(this).attr('id')+'_'+$.randomString(8));
        }

        if (typeof $(this).attr('name') != 'undefined') {
            $(this).attr('name',$(this).attr('name')+'_'+$.randomString(8));
        }

        that.loopThrough($(this).children());
    });

    return $element;
};

/**
 * @returns {*}
 */
Replica.prototype.getLastReplicaId = function() {

    var that = this;

    return that.$lastReplica.attr('id');
};

/**
 * @returns {Echo}
 * @constructor
 */
var Echo = function() {

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
Echo.prototype.bind = function($source, $target, eventsArray) {

    if (!eventsArray instanceof Array) {
        throw new Error('eventsArray must be an array containing events\' names triggering echo');
    }

    var that = this;

    if (that.bound) {
        throw new Error('Echo already bound, to unbind use Echo::unbind()');
    }

    that.$source = $source;
    that.$target = $target;
    that.events = eventsArray.join(' ');

    that.$source.on(that.events, function(e) {
        that.$target.vv(that.$source.vv());
    });

    that.bound = true;
};

Echo.prototype.unbind = function() {

    var that = this;

    that.$source.off();
};

/**
 * @returns {Modal}
 * @constructor
 */
var Modal = function() {

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
Modal.prototype.setTitle = function(title) {
    this.header.html(title);
};

/**
 * @param text
 */
Modal.prototype.setOkButton = function(text) {
    this.okButton.html(text);
};

/**
 * @param text
 */
Modal.prototype.setCancelButton = function(text) {
    this.cancelButton.html(text);
};

/**
 * @param content
 */
Modal.prototype.setContent = function(content) {
    this.body.html(content);
};

Modal.prototype.appendContent = function(content) {
    this.body.append(content);
};

Modal.prototype.prependContent = function(content) {
    this.body.prepend(content);
};

Modal.prototype.hide = function() {
    this.modal.modal('hide');
};

Modal.prototype.show = function() {
    this.modal.modal('show');
};

/**
 * @param dzConf
 * @returns {Modal}
 */
Modal.prototype.addDropZone = function(dzConf) {

    this.setContent('' +
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
        '<div class="fallback">'+
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>');

    this.dz = new Dropzone('#dropzoneModal', dzConf);

    return this;
};

Modal.prototype.attachDropZoneEvent = function(event, callback) {
    this.dz.on(event,callback);
};

Modal.prototype.detachDropZoneEvent = function(event) {
    this.dz.off(event);
};

/**
 * @param newvalue
 * @returns {*|jQuery}
 */
$.fn.vv = function(newvalue) {
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
$.randomString = function(l) {
    return Math.round((Math.pow(36, l + 1) - Math.random() * Math.pow(36, l))).toString(36).slice(1);
};

$.drawUI = function() {

    var buttons = $('bs-button');

    $.each(buttons, function() {
        var button = new Button($(this).data());
        button.draw($(this));
    });

    $('[data-init-plugin=selectize]').each(function() {
        $(this).selectize({
            create: false,
            dropdownDirection: 'auto'
        });
        $('.selectize-dropdown-content').scrollbar();
    });

    $(function(){ $('.color-picker').colorpicker(); });

    $('textarea.summer').summernote({
        lang: "it-IT",
        height: 100
    });
};