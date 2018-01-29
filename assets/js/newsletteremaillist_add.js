(function ($) {
    Pace.ignore(function () {

        // select per la composizione della query
        var select = $('#filteredField');
        var newOptions = {
            'empty':'',
            'clean': 'Pulisci',
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
        //inizializzo i campi a casccata
        let inputAgeFieldCounter =0;
        let inputCountryFieldCounter =0;
        let inputCityFieldCounter =0;
        $("#filteredField").change(function () {
            var selection = $(this).val();

            if (selection == 'birthDate') {
               // $("#inputAge").empty();
                inputAgeFieldCounter=inputAgeFieldCounter+1;

                $("#inputAge").append('<div class=\"row\">' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterAge'+inputAgeFieldCounter+'\">Seleziona l\'Eta </label><select id=\"filterAge'+inputAgeFieldCounter+'\" name=\"filterAge'+inputAgeFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione l\'eta\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"\"></option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\')) BETWEEN 18 AND 24 \">18-24</option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\')) BETWEEN 25 AND 34 \">25-34</option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\')) BETWEEN 35 AND 44  \">35-44</option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\')) BETWEEN 45 AND 54  \">45-54</option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\')) BETWEEN 55 AND 64  \">55-64</option>' +
                    '<option value=\"(DATE_FORMAT(NOW(), \'%Y\')-DATE_FORMAT(ud.birthDate, \'%Y\'))  >=65\">+65</option></select>' +
                    ' </div>' +
                    '</div>' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterAgeAndOr'+inputAgeFieldCounter+'\">Seleziona la condizione </label><select id=\"filterAgeAndOr'+inputAgeFieldCounter+'\" name=\"filterAgeAndOr'+inputAgeFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la copndizione\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"\"></option>' +
                    '<option value=\" AND \">(AND) = COMPRESO </option>' +
                    '<option value=\" OR \">(OR) = OPPURE</option>' +
                    '<option value=\" NOT \">(NOT) = NON COMPRESO</option>' +
                    '<option value=\" XOR \">(XOR) = ESCLUSIVO</option></select>' +

                    ' </div>' +
                    '</div>' +

                    '</div>');
                var jqueryFilterAge = "#"+"filterAge"+inputAgeFieldCounter.toString();
                var jqueryFilterAgeAndOr = "#"+"filterAgeAndOr"+inputAgeFieldCounter.toString();



                $(jqueryFilterAgeAndOr).change(function () {

                    var selectionFilter = $(jqueryFilterAge).val();

                    var selectionFilterAndOr = $(jqueryFilterAgeAndOr).val();
                    var resultSelectionFilter = selectionFilterAndOr + " " + selectionFilter;
                    if ($("#filterAge").val()==""){
                        $("#filterAge").val(resultSelectionFilter) ;
                    }else{
                        $('#filterAge').val($('#filterAge').val() + " " + resultSelectionFilter);
                    }


                });


            } else if (selection == 'city') {

                inputCityFieldCounter=inputCityFieldCounter+1;
                $("#inputCity").append('<div class=\"row\">' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCity'+inputCityFieldCounter+'\">Seleziona la Città </label><select id=\"filterCity'+inputCityFieldCounter+'\" name=\"filterCity'+inputCityFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la citta\"' +
                    'data-init-plugin=\"selectize\"></select>' +
                    ' </div>' +
                    '</div>' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCityAndOr'+inputCityFieldCounter+'\">Seleziona la condizione </label><select id=\"filterCityAndOr'+inputCityFieldCounter+'\" name=\"filterCityAndOr'+inputCityFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la copndizione\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"\"></option>' +
                    '<option value=\" AND \">(AND) = COMPRESO </option>' +
                    '<option value=\" OR \">(OR) = OPPURE</option>' +
                    '<option value=\" NOT \">(NOT) = NON COMPRESO</option>' +
                    '<option value=\" XOR \">(XOR) = ESCLUSIVO</option></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');
                var jqueryFilterCity = "#"+"filterCity"+inputCityFieldCounter.toString();
                var jqueryFilterCityAndOr = "#"+"filterCityAndOr"+inputCityFieldCounter.toString();



                $(jqueryFilterCityAndOr).change(function () {

                    var selectionFilter = $(jqueryFilterCity).val();

                    var selectionFilterAndOr = $(jqueryFilterCityAndOr).val();
                    var resultSelectionFilter = selectionFilterAndOr + " " + "city LIKE '%"+selectionFilter+"%'";
                    if ($("#filterCity").val()==""){
                        $("#filterCity").val(resultSelectionFilter) ;
                    }else{
                        $('#filterCity').val($('#filterCity').val() + " " + resultSelectionFilter);
                    }


                });
                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'City'
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $(jqueryFilterCity);
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'name',
                        labelField: 'name',
                        searchField: 'name',
                        options: res2,
                    });
                });


            } else if (selection == 'country') {
                inputCountryFieldCounter=inputCountryFieldCounter+1;

                $("#inputCountry").append('<div class=\"row\">' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCountry'+inputCountryFieldCounter+'\">Seleziona la Nazione </label><select id=\"filterCountry'+inputCountryFieldCounter+'\" name=\"filterCountry'+inputCountryFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la nazione\"' +
                    'data-init-plugin=\"selectize\"></select>' +
                    ' </div>' +
                    '</div>' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterCountryAndOr'+inputCountryFieldCounter+'\">Seleziona la condizione </label><select id=\"filterCountryAndOr'+inputCountryFieldCounter+'\" name=\"filterCountryAndOr'+inputCountryFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la copndizione\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"\"></option>' +
                    '<option value=\" AND ua.countryId = \">(AND) = COMPRESO </option>' +
                    '<option value=\" OR ua.countryId=\">(OR) = OPPURE</option>' +
                    '<option value=\" NOT ua.countryId=\">(NOT) = NON COMPRESO</option>' +
                    '<option value=\" XOR ua.countryId= \">(XOR) = ESCLUSIVO</option></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');
                var jqueryFilterCountry = "#"+"filterCountry"+inputCountryFieldCounter.toString();
                var jqueryFilterCountryAndOr = "#"+"filterCountryAndOr"+inputCountryFieldCounter.toString();



                $(jqueryFilterCountryAndOr).change(function () {

                    var selectionFilter = $(jqueryFilterCountry).val();

                    var selectionFilterAndOr = $(jqueryFilterCountryAndOr).val();
                    var resultSelectionFilter = selectionFilterAndOr + " " + selectionFilter;
                    if ($("#filterCountry").val()==""){
                        $("#filterCountry").val(resultSelectionFilter) ;
                    }else{
                        $('#filterCountry').val($('#filterCountry').val() + " " + resultSelectionFilter);
                    }
                });
                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'Country'
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $(jqueryFilterCountry);
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
                    '<option value=\"and nu.isActive=1\">ISCRITTI</option>' +
                    '<option value=\"and nu.isActive=\'0\'\">NON ISCRITTI</option>' +
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
                $("#filterAge").val("");
                $("#filterCity").val("");
                $("#filterCountry").val("");
                 inputAgeFieldCounter =0;
                 inputCountryFieldCounter = 0;
                 inputCityFieldCounter = 0;


            } else if (selection == 'gender') {
                $("#inputGender").empty();

                $("#inputGender").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterGender\">Seleziona il Sesso </label><select id=\"filterGender\" name=\"filterGender\" class=\"full-width selectpicker\" placeholder=\"Selezione il Sesso"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"AND ud.gender=\'M\'\">Sesso Maschile</option>' +
                    '<option value=\"AND ud.gender=\'F\'\">Sesso Femminile</option>' +
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
                table: 'NewsletterGroup'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterGroupId');
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
    let bsModal = new $.bsModal('Salva il Gruppo', {
        body: '<div><p>Premere ok per Salvare il Gruppo' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let filterGender = $('#filterGender').val();
        let filterAge = $('#filterAge').val();
        let filterCity = $('#filterCity').val();
        let filterCountry = $('#filterCountry').val();
        let filterIsActive = $('#filterIsActive').val();
        let filterOrderDateStart = $('#filterOrderDateStart').val();
        let filterOrderDateFinish = $('#filterOrderDateFinish').val();
        if (typeof filterGender === "undefined") {
            filterGender = "";

        } else {
            filterGender = filterGender;
        }
        if (typeof filterAge === "undefined") {
            filterAge = "";
        } else {
            filterAge = filterAge;
        }
        if (typeof filterCity === "undefined") {
            filterCity = "";
        } else {
            filterCity =  filterCity ;
        }
        if (typeof filterCountry === "undefined") {
            filterCountry = "";
        } else {
            filterCountry = filterCountry ;
        }
        if (typeof filterIsActive === "undefined") {
            filterIsActive = "";
        } else {
            filterIsActive = "AND nu.isActive='" + filterIsActive + "'";
        }
        if (typeof filterOrderDateStart === "undefined") {
            filterOrderDateStart = "";

        } else {
            filterOrderDateStart =  filterOrderDateStart;
        }
        if (typeof filterOrderDateFinish === "undefined") {
            filterOrderDateFinish = "";
        } else {
            filterOrderDateFinish =  filterOrderDateFinish ;
        }
        let groupby=" GROUP BY nu.id"
       let filterOrderDateExclude = "";
        if (filterOrderDateStart == "" || filterOrderDateFinish == "") {
            filterOrderDateExclude = "";
        } else {
            filterOrderDateExclude = "AND o.orderDate NOT BETWEEN '" + filterOrderDateStart + "' AND  '" + filterOrderDateFinish + "'";
        }


        const data = {
            name: $('#name').val(),
            sql: filterGender + ' ' + filterAge + ' ' + filterCity + ' ' + filterCountry + ' ' + filterIsActive + ' ' + filterOrderDateExclude + groupby,
            newsletterGroupId: $('#newsletterGroupId').val(),

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




