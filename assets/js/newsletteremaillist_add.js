(function ($) {
    Pace.ignore(function () {

        // select per la composizione della query
        var select = $('#filteredField');
        var newOptions = {
            'birthDate': 'Età',
            'city': 'Città',
            'country': 'Nazione',
            'isActive': 'Utenti Iscritti',
            'orderDate': 'Esclusione Periodo Ordini'
        };
        $('option', select).remove();
        $.each(newOptions, function (text, key) {
            var option = new Option(key, text);
            select.append($(option));
        });
        $("#filteredField").change(function () {
            var selection = $(this).val();
            if (selection == 'birthDate') {
                $("#inputAge").empty();


                $("#inputAge").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterAge\">Seleziona l\'Eta </label><select id=\"filterAge\" name=\"filterAge\" class=\"full-width selectpicker\" placeholder=\"Selezione l\'eta\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"and dataAge>=18 and dataAge<=24\">18-24</option>' +
                    '<option value=\"and dataAge>=25 and dataAge<=34\">25-34</option>' +
                    '<option value=\"and dataAge>=35 and dataAge<=44\">35-44</option>' +
                    '<option value=\"and dataAge>=45 and dataAge<=54\">55-64</option>' +
                    '<option value=\"and dataAge>=65\">55-64</option></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

            } else if (selection == 'city') {
                $("#inputCountry").empty();
                $("#inputCity").empty();

                $("#inputCity").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCity\">Seleziona la Città </label><select id=\"filterCity\" name=\"filterCity\" class=\"full-width selectpicker\" placeholder=\"Selezione la citta\"' +
                    'data-init-plugin=\"selectize\"></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'City'
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $('#filterCity');
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: res2,
                    });
                });

            } else if (selection == 'country') {
                $("#inputCountry").empty();
                $("#inputCity").empty();

                $("#inputCountry").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCountry\">Seleziona la Nazione </label><select id=\"filterCountry\" name=\"filterCountry\" class=\"full-width selectpicker\" placeholder=\"Selezione la nazione\"' +
                    'data-init-plugin=\"selectize\"></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'Country'
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $('#filterCountry');
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: res2,
                    });
                });

            } else if (selection == 'isActive') {
                $("#inputIsActive").empty();

                $("#inputIsActive").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterisActive\">Seleziona se Utenti Iscritti </label><select id=\"filterisActive\" name=\"filterisActive\" class=\"full-width selectpicker\" placeholder=\"Selezione se Iscritti"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"and isActive=1\">ISCRITTI</option>' +
                    '<option value=\"and isActive=\'0\'\">NON ISCRITTI</option>' +
                    '</select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

            } else if (selection == 'orderDate') {
                $("#inputOrderDate").empty();

                $("#inputOrderDate").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterOrdeDateStart\">Seleziona Esclusione Ordini da Data </label><input type="\date\" id=\"filterOrdeDateStart\" name=\"filterOrdeDateStart\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini da Data "' +
                    'value=\"\"/>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');
                $("#inputOrderDate").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterOrdeDateFinish\">Seleziona Esclusione Ordini a Data </label><input type="\date\" id=\"filterOrdeDateFinish\" name=\"filterOrdeDateFinish\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini a Data "' +
                    'value=\"\"/>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

            }

        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterEmailList'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterEmailListId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterTemplate'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterTemplateId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'template',
                searchField: ['template'],
                options: res2,
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#campaignId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
    });
})(jQuery);

$(document).on('bs.newNewsletterEmailList.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Salvare la Newsletter' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            name: $('#name').val(),
            fromEmailAddressId: $('#fromEmailAddressId').val(),
            sendAddressDate: $('#sendAddressDate').val(),
            newsletterEmailListId: $('#newsletterEmailListId').val(),
            newsletterTemplateId: $('#newsletterTemplateId').val(),
            subject: $('#subject').val(),
            dataDescription: $('#dataDescription').val(),
            preCompiledTemplate: $('#preCompiledTemplate').val(),
            campaignId: $('#campaignId').val()
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterEmailList',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});




