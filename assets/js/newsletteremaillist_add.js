(function ($) {
    Pace.ignore(function () {

        // select per la composizione della query
        var select = $('#filteredField');
        var newOptions = {
            'clean' :'Pulisci',
            'gender': 'Sesso',
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
                    '<option value=\"and a.dataAge>=18 and a.dataAge<=24\">18-24</option>' +
                    '<option value=\"and a.dataAge>=25 and a.dataAge<=34\">25-34</option>' +
                    '<option value=\"and a.dataAge>=35 and a.dataAge<=44\">35-44</option>' +
                    '<option value=\"and a.dataAge>=45 and a.dataAge<=54\">45-54</option>' +
                    '<option value=\"and a.dataAge>=55 and a.dataAge<=64\">55-64</option>' +
                    '<option value=\"and dataAge>=65\">+65</option></select>' +
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
                        valueField: 'name',
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
                    '<label for=\"filterOrderDateStart\">Seleziona Esclusione Ordini da Data </label><input type="\date\" id=\"filterOrderDateStart\" name=\"filterOrderDateStart\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini da Data "' +
                    'value=\"\"/>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');
                $("#inputOrderDate").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterOrderDateFinish\">Seleziona Esclusione Ordini a Data </label><input type="\date\" id=\"filterOrderDateFinish\" name=\"filterOrderDateFinish\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini a Data "' +
                    'value=\"\"/>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');

            } else if (selection == 'clean') {
                $("#inputOrderDate").empty();
                $("#inputIsActive").empty();
                $("#inputCity").empty();
                $("#inputCountry").empty();
                $("#inputAge").empty();



            }else if (selection == 'gender') {
                $("#inputGender").empty();

                $("#inputGender").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterGender\">Seleziona il Sesso </label><select id=\"filterGender\" name=\"filterGender\" class=\"full-width selectpicker\" placeholder=\"Selezione il Sesso"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"and a.gender=\'M\'\">Sesso Maschile</option>' +
                    '<option value=\"and a.gender=\'F\'\">Sesso Femminile</option>' +
                    '</select>' +
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

    });
})(jQuery);

$(document).on('bs.newNewsletterEmailList.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Salvare la Newsletter' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let filterGender =$('#filterGender').val();
        let filterAge =  $('#filterAge').val();
        let filterCity = $('#filterCity').val();
        let filterCountry = $('#filterCountry').val();
        let filterIsActive = $('#filterIsActive').val();
        let filterOrderDateStart = $('#filterOrderDateStart').val();
        let filterOrderDateFinish = $('#filterOrderDateFinish').val();
        if (typeof filterGender === "undefined"){
            filterGender="";

        }else{
            filterGender=filterGender;
        }
        if (typeof filterAge === "undefined") {
            filterAge = "";
        }else{
            filterAge = filterAge;
        }
        if (typeof filterCity === "undefined"){
            filterCity="";
        }else{
            filterCity="and b.city='"+filterCity+"'";
        }
        if (typeof filterCountry === "undefined"){
            filterCountry="";
        }else{
           filterCountry="and b.countryId='"+filterCountry+"'";
        }
        if (typeof filterIsActive === "undefined"){
            filterIsActive="";
        }else{
            filterIsActive="and c.isActive='"+filterIsActive+"'";
        }
        if (typeof filterOrderDateStart === "undefined"){
            filterOrderDateStart="";

        }else{
            filterOrderDateStart="and d.orderDate>='"+filterOrderDateStart+"'";
        }
        if (typeof filterOrderDateFinish === "undefined"){
            filterOrderDateFinish="";
        }else{
            filterOrderDateFinish="and d.orderDate<='"+filterOrderDateFinish+"'";
        }




        const data = {
            name: $('#name').val(),
            sql: filterGender + ' ' + filterAge + ' ' + filterCity + ' ' + filterCountry + ' ' + filterIsActive + ' ' + filterOrderDateStart + ' ' + filterOrderDateFinish,
            newsletterEmailListId: $('#newsletterEmailListId').val()
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterEmailListManage',
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




