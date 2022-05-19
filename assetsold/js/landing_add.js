$('textarea.summer').summernote({
    lang: "it-IT",
    height: 100
});

$(document).ready(function() {
   $('[data-json="code"]').val($.randomString(6));
});

var sr = new Replica($('#section'));
var tr = new Replica($('[id^="style"]'));
var lr = new Replica($('[id^="link"]'));

$(document).on('bs.replica.section',function() {

    var echo = new Echo();
    var brandEcho = new Echo();
    var categoryEcho = new Echo();

    var $e = sr.replicateInto($('.replicaContainer'));
    var initialStyleId = $('[id^="style_"]').last().attr('id');

    $('.landing-summary .panel-body').append(
        '<div class="section-recap" data-bind="'+sr.getLastReplicaId()+'">'+
        '<p><span class="replicaTitle">&nbsp;</span></p>'+
        '<p class="replicaStyle" data-bind="'+initialStyleId+'">'+
        '<span class="replicaStyleBrand">&nbsp;</span> - <span class="replicaStyleCategory">&nbsp;</span>'+
        ' - <span class="replicaStyleTotal">&nbsp;</span></p>' +
        '</div>');

    echo.bind(
        $e.find('[data-json="section.title"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaTitle'),
        ["keyup"]
    );

    brandEcho.bind(
        $e.find('[data-json="style.brand"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaStyleBrand'),
        ["change"]
    );

    categoryEcho.bind(
        $e.find('[data-json="style.category"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaStyleCategory'),
        ["change"]
    );
});

$(document).on('bs.replica.style',function(evt, button, html, parentEvt) {

    var $e = tr.replicateInto($('#'+$(button).parentsUntil('[id^="section_"]').parent().attr('id')+' .styleRepeat'));

    $('.section-recap[data-bind="'+sr.getLastReplicaId()+'"]').append(
        '<p class="replicaStyle" data-bind="'+tr.getLastReplicaId()+'">'+
        '<span class="replicaStyleBrand">&nbsp;</span> - <span class="replicaStyleCategory">&nbsp;</span>'+
        ' - <span class="replicaStyleTotal">&nbsp;</span></p>'
    );

    var brandEcho = new Echo();
    var categoryEcho = new Echo();

    brandEcho.bind(
        $e.find('[data-json="style.brand"]'),
        $('.replicaStyle[data-bind="'+tr.getLastReplicaId()+'"] .replicaStyleBrand'),
        ["change"]
    );

    categoryEcho.bind(
        $e.find('[data-json="style.category"]'),
        $('.replicaStyle[data-bind="'+tr.getLastReplicaId()+'"] .replicaStyleCategory'),
        ["change"]
    );
});


$(document).on('bs.replica.link', function() {
    lr.replicateInto($('.linkReplicaContainer'));
});

$(document).on('bs.save.landing', function() {

    var date = new Date();

    var landing = {};

    var data = {
        background: "back.png",
        sections: [],
        links: []
    };

    var section = {
        style: []
    };

    var link = {};

    $('.landing-header').find('[data-json]').each(function() {
        data[$(this).data('json')] = $(this).vv();
    });

    $('.canonical').find('[data-json]').each(function() {
        data[$(this).data('json')] = $(this).vv();
    });

    data['youmightlike'] = $('[data-json="youmightlike"]').val();
    data.creationDate = date.toISOString();
    data.updateDate = date.toISOString();

    $('[id^="link_"]').each(function() {

        var i = 1;
        $(this).find('[data-json^="links."]').each(function() {
            link[$(this).data('json').split('.')[1]] = $(this).vv();
            if (i%2 == 0) {
                data.links.push(link);
                link = {};
            }
            i++;
        });
    });

    $('[id^=section_]').each(function() {

        $(this).find('[data-json^="section."]').each(function() {
            if ($(this).hasClass('summer')) {
                section[$(this).data('json').split('.')[1]] = $(this).code();
            } else {
                section[$(this).data('json').split('.')[1]] = $(this).vv();
            }
        });

        $(this).find('[id^="style_"]').each(function() {

            var style = {};

            $(this).find('[data-json^="style."]').each(function() {
                if ($(this).hasClass('summer')) {
                    style[$(this).data('json').split('.')[1]] = $(this).code();
                } else {
                    style[$(this).data('json').split('.')[1]] = $(this).val();
                }
            });

            section.style.push(style);
        });

        data.sections.push(section);
    });

    landing[data.code] = data;

    delete(landing[data.code].code);

    var loader = new Alert({
        type: "info",
        loader: true,
        dismissable: false,
        selfClose: false,
        message: "Salvataggio in corso..."
    });
    loader.open();

    $.ajax({
        url: '/blueseal/marketing/landing/aggiungi',
        data: landing,
        type: 'post'
    }).done(function () {
        loader.close();
        console.log(landing);
        new Alert({
            type: "success",
            message: "Landing page salvata con successo"
        }).open();
    }).fail(function() {
        loader.close();
        new Alert({
            type: "danger",
            message: "Errore di comunicazione"
        }).open();
    });
});

$(document).on('bs.landing.randomcode', function() {
    $('[data-json="code"]').val($.randomString(6));
});