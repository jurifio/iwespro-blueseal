var sr = new Replica($('#section'));
var fr = new Replica($('[id^="field"]'));

$(document).ready(function() {

    var echo = new Echo();
    var modifierEcho = new Echo();
    var fieldEcho = new Echo();
    var operatorEcho = new Echo();
    var connectorEcho = new Echo();
    var valueEcho = new Echo();

    var $e = sr.replicateInto($('.replicaContainer'));
    var initialFieldId = $('[id^="field_"]').last().attr('id');

    $('.section-summary .panel-body').append(
        '<div class="section-recap" data-bind="'+sr.getLastReplicaId()+'">'+
        '<p><span class="replicaTitle">&nbsp;</span></p>'+
        '<p class="replicaField" data-bind="'+initialFieldId+'">'+
        '<span class="replicaFieldModifier">&nbsp;</span> - <span class="replicaFieldField">&nbsp;</span>'+
        ' - <span class="replicaFieldOperator">&nbsp;</span> - <span class="replicaFieldConnector">&nbsp;</span>'+
        ' - <span class="replicaFieldValue">&nbsp;</span>'+
        ' - <span class="replicaFieldTotal">&nbsp;</span></p>' +
        '</div>');

    echo.bind(
        $e.find('[data-json="section.title"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaTitle'),
        ["keyup"]
    );

    modifierEcho.bind(
        $e.find('[data-json="field.modifier"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldModifier'),
        ["change"]
    );

    fieldEcho.bind(
        $e.find('[data-json="field.field"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldField'),
        ["change"]
    );

    operatorEcho.bind(
        $e.find('[data-json="field.operator"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldOperator'),
        ["change"]
    );

    connectorEcho.bind(
        $e.find('[data-json="field.connector"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldConnector'),
        ["change"]
    );

    valueEcho.bind(
        $e.find('[data-json="field.value"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldValue'),
        ["keyup"]
    );
});

$(document).on('bs.replica.field',function(evt, button, html, parentEvt) {

    var $e = fr.replicateInto($('#'+$(button).parentsUntil('[id^="section_"]').parent().attr('id')+' .fieldReplicaContainer'));

    $('.section-recap[data-bind="'+sr.getLastReplicaId()+'"]').append(
        '<p class="replicaField" data-bind="'+fr.getLastReplicaId()+'">'+
        '<span class="replicaFieldModifier">&nbsp;</span> - <span class="replicaFieldField">&nbsp;</span>'+
        ' - <span class="replicaFieldOperator">&nbsp;</span> - <span class="replicaFieldConnector">&nbsp;</span>'+
        ' - <span class="replicaFieldValue">&nbsp;</span>'+
        ' - <span class="replicaFieldTotal">&nbsp;</span></p>'
    );

    var modifierEcho = new Echo();
    var fieldEcho = new Echo();
    var operatorEcho = new Echo();
    var connectorEcho = new Echo();
    var valueEcho = new Echo();

    modifierEcho.bind(
        $e.find('[data-json="field.modifier"]'),
        $('.replicaField[data-bind="'+fr.getLastReplicaId()+'"] .replicaFieldModifier'),
        ["change"]
    );

    fieldEcho.bind(
        $e.find('[data-json="field.field"]'),
        $('.replicaField[data-bind="'+fr.getLastReplicaId()+'"] .replicaFieldField'),
        ["change"]
    );
    operatorEcho.bind(
        $e.find('[data-json="field.operator"]'),
        $('.replicaField[data-bind="'+fr.getLastReplicaId()+'"] .replicaFieldOperator'),
        ["change"]
    );

    connectorEcho.bind(
        $e.find('[data-json="field.connector"]'),
        $('.replicaField[data-bind="'+fr.getLastReplicaId()+'"] .replicaFieldConnector'),
        ["change"]
    );
    valueEcho.bind(
        $e.find('[data-json="field.value"]'),
        $('.replicaField[data-bind="'+fr.getLastReplicaId()+'"] .replicaFieldValue'),
        ["keyup"]
    );
});

$(document).on('bs.replica.section',function() {

    var echo = new Echo();
    var modifierEcho = new Echo();
    var fieldEcho = new Echo();
    var operatorEcho = new Echo();
    var connectorEcho = new Echo();
    var valueEcho = new Echo();

    var $e = sr.replicateInto($('.replicaContainer'));
    var initialFieldId = $('[id^="field_"]').last().attr('id');

    $('.section-summary .panel-body').append(
        '<div class="section-recap" data-bind="'+sr.getLastReplicaId()+'">'+
        '<p><span class="replicaTitle">&nbsp;</span></p>'+
        '<p class="replicaField" data-bind="'+initialFieldId+'">'+
        '<span class="replicaFieldModifier">&nbsp;</span> - <span class="replicaFieldField">&nbsp;</span>'+
        ' - <span class="replicaFieldOperator">&nbsp;</span> - <span class="replicaFieldConnector">&nbsp;</span>'+
        ' - <span class="replicaFieldValue">&nbsp;</span>'+
        ' - <span class="replicaFieldTotal">&nbsp;</span></p>' +
        '</div>');

    echo.bind(
        $e.find('[data-json="section.title"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaTitle'),
        ["keyup"]
    );

    modifierEcho.bind(
        $e.find('[data-json="field.modifier"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldModifier'),
        ["change"]
    );

    fieldEcho.bind(
        $e.find('[data-json="field.field"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldField'),
        ["change"]
    );

    operatorEcho.bind(
        $e.find('[data-json="field.operator"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldOperator'),
        ["change"]
    );

    connectorEcho.bind(
        $e.find('[data-json="field.connector"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldConnector'),
        ["change"]
    );

    valueEcho.bind(
        $e.find('[data-json="field.value"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaFieldValue'),
        ["keyup"]
    );
});

$(document).on('bs.save.connector', function() {

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