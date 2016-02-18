var sr = new Replica($('#section'));
var fr = new Replica($('[id^="field"]'));

$(document).ready(function() {

    var sizeEcho = new Echo();
    var modifierEcho = new Echo();
    var fieldEcho = new Echo();
    var operatorEcho = new Echo();
    var connectorEcho = new Echo();
    var valueEcho = new Echo();

    var $e = sr.replicateInto($('.replicaContainer'));
    var initialFieldId = $('[id^="field_"]').last().attr('id');

    sizeEcho.bind(
        $e.find('[data-json="section.size"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaSize'),
        ["change"]
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

    var sizeEcho = new Echo();
    var modifierEcho = new Echo();
    var fieldEcho = new Echo();
    var operatorEcho = new Echo();
    var connectorEcho = new Echo();
    var valueEcho = new Echo();

    var $e = sr.replicateInto($('.replicaContainer'));
    var initialFieldId = $('[id^="field_"]').last().attr('id');

    sizeEcho.bind(
        $e.find('[data-json="section.size"]'),
        $('.section-recap[data-bind="'+$e.attr('id')+'"] .replicaSize'),
        ["change"]
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

    var data = {
        sections: []
    };


    $('[id^="section_"]').each(function() {

        var section = {
            size: [],
            field: []
        };

        $(this).find('[data-json^="section."]').each(function() {
            section[$(this).data('json').split('.')[1]] = $(this).val();
        });

        $(this).find('[id^="field_"]').each(function() {

            var field = {};

            $(this).find('[data-json^="field."]').each(function() {
                field[$(this).data('json').split('.')[1]] = $(this).val();
            });

            section.field.push(field);
        });

        data.sections.push(section);
    });

    var loader = new Alert({
        type: "info",
        loader: true,
        dismissable: false,
        selfClose: false,
        message: "Salvataggio in corso..."
    });
    loader.open();

    $.ajax({
        url: '#',
        data: data,
        type: 'post'
    }).done(function () {
        loader.close();
        console.log(connector);
        new Alert({
            type: "success",
            message: "Connettore salvato con successo"
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