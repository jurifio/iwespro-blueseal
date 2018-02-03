(function ($) {
    Pace.ignore(function () {

        $('#buttonOpen').on('click', function () {
            var resultSelectionFilter =  " ("  ;
            if ($("#filterQuery").val()==""){
                $("#filterQuery").val(resultSelectionFilter) ;
            }else{
                $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
            }
        });
        $('#buttonClose').on('click', function () {
            var resultSelectionFilter =  ") "  ;
            if ($("#filterQuery").val()==""){
                $("#filterQuery").val(resultSelectionFilter) ;
            }else{
                $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
            }
        });
        $('#buttonAnd').on('click', function () {
            var resultSelectionFilter =  " AND  "  ;
            if ($("#filterQuery").val()==""){
                $("#filterQuery").val(resultSelectionFilter) ;
            }else{
                $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
            }
        });
        $('#buttonOr').on('click', function () {
            var resultSelectionFilter =  " OR  "  ;
            if ($("#filterQuery").val()==""){
                $("#filterQuery").val(resultSelectionFilter) ;
            }else{
                $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
            }
        });

        //inizializzo i campi a cascata
        let inputGenderFieldCounter =0;
        let inputAgeFieldCounter =0;
        let inputCountryFieldCounter =0;
        let inputCityFieldCounter =0;
        $('#buttonAge').on('click', function () {

            inputAgeFieldCounter=inputAgeFieldCounter+1;


                $("#inputQuery").append('<div class=\"row\">' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterAgeAndOr' + inputAgeFieldCounter + '\">Seleziona la condizione </label><select id=\"filterAgeAndOr' + inputAgeFieldCounter + '\" name=\"filterAgeAndOr' + inputAgeFieldCounter + '\" class=\"full-width selectpicker\" placeholder=\"Selezione la copndizione\"' +
                    'data-init-plugin=\"selectize\">' +
                    '<option value=\"\"></option>' +
                    '<option value=\" AND \">(AND) = COMPRESO </option>' +
                    '<option value=\" OR \">(OR) = OPPURE</option>' +
                    '<option value=\" NOT \">(NOT) = NON COMPRESO</option>' +
                    '<option value=\" XOR \">(XOR) = ESCLUSIVO</option></select>' +

                    ' </div>' +
                    '</div>' +
                    ' <div class="col-md-6">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"filterAge' + inputAgeFieldCounter + '\">Seleziona l\'Eta </label><select id=\"filterAge' + inputAgeFieldCounter + '\" name=\"filterAge' + inputAgeFieldCounter + '\" class=\"full-width selectpicker\" placeholder=\"Selezione l\'eta\"' +
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


                    '</div>');
                var jqueryFilterAge = "#" + "filterAge" + inputAgeFieldCounter.toString();
                var jqueryFilterAgeAndOr = "#" + "filterAgeAndOr" + inputAgeFieldCounter.toString();


                $(jqueryFilterAge).change(function () {

                    var selectionFilter = $(jqueryFilterAge).val();

                    var selectionFilterAndOr = $(jqueryFilterAgeAndOr).val();
                    var resultSelectionFilter = selectionFilterAndOr + " " + selectionFilter;
                    if ($("#filterQuery").val() == "") {
                        $("#filterQuery").val(resultSelectionFilter);
                    } else {
                        $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
                    }

                    //  $('#filteredField option[value=vuoto]').attr('selected','selected');


                });

        });
        $('#buttonGender').on('click', function () {
            inputGenderFieldCounter=inputGenderFieldCounter+1
            $("#inputQuery").append('<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterGender' + inputGenderFieldCounter + '\">Seleziona l\'Eta </label><select id=\"filterGender' + inputGenderFieldCounter + '\" name=\"filterGender' + inputGenderFieldCounter + '\" class=\"full-width selectpicker\" placeholder=\"Selezione il Sesso\"' +
                'data-init-plugin=\"selectize\">' +
                '<option value=\"\"></option>' +
                '<option value=\"AND ud.gender=\'M\'\">Sesso Maschile</option>' +
                '<option value=\"AND ud.gender=\'F\'\">Sesso Femminile</option>' +
                '</select>' +
                ' </div>' +
                '</div>' +
                '</div>');

            var jqueryFilterGender = "#" + "filterGender" + inputGenderFieldCounter.toString();
            $(jqueryFilterGender).change(function () {

                var selectionFilter = $(jqueryFilterGender).val();


                var resultSelectionFilter =  " " + selectionFilter ;
                if ($("#filterQuery").val()==""){
                    $("#filterQuery").val(resultSelectionFilter) ;
                }else{
                    $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
                }


            });
        });
        $('#buttonCity').on('click', function () {
            inputCityFieldCounter=inputCityFieldCounter+1;
            $("#inputQuery").append('<div class=\"row\">' +
                ' <div class="col-md-6">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterCityAndOr'+inputCityFieldCounter+'\">Seleziona la condizione </label><select id=\"filterCityAndOr'+inputCityFieldCounter+'\" name=\"filterCityAndOr'+inputCityFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la condizione\"' +
                'data-init-plugin=\"selectize\">' +
                '<option value=\"\"></option>' +
                '<option value=\" AND \">(AND) = COMPRESO </option>' +
                '<option value=\" OR \">(OR) = OPPURE</option>' +
                '<option value=\" NOT \">(NOT) = NON COMPRESO</option>' +
                '<option value=\" XOR \">(XOR) = ESCLUSIVO</option></select>' +
                ' </div>' +
                '</div>' +
                ' <div class="col-md-6">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterCity'+inputCityFieldCounter+'\">Seleziona la Città </label><select id=\"filterCity'+inputCityFieldCounter+'\" name=\"filterCity'+inputCityFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la citta\"' +
                'data-init-plugin=\"selectize\"></select>' +
                ' </div>' +
                '</div>' +

                '</div>');
            var jqueryFilterCity = "#"+"filterCity"+inputCityFieldCounter.toString();
            var jqueryFilterCityAndOr = "#"+"filterCityAndOr"+inputCityFieldCounter.toString();



            $(jqueryFilterCity).change(function () {

                var selectionFilter = $(jqueryFilterCity).val();

                var selectionFilterAndOr = $(jqueryFilterCityAndOr).val();
                var resultSelectionFilter = selectionFilterAndOr + " " + "city LIKE '%"+selectionFilter+"%'";
                if ($("#filterQuery").val()==""){
                    $("#filterQuery").val(resultSelectionFilter) ;
                }else{
                    $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
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
        });

        $('#buttonCountry').on('click', function () {
            inputCountryFieldCounter=inputCountryFieldCounter+1;

            $("#inputQuery").append('<div class=\"row\">' +
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
                ' <div class="col-md-6">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterCountry'+inputCountryFieldCounter+'\">Seleziona la Nazione </label><select id=\"filterCountry'+inputCountryFieldCounter+'\" name=\"filterCountry'+inputCountryFieldCounter+'\" class=\"full-width selectpicker\" placeholder=\"Selezione la nazione\"' +
                'data-init-plugin=\"selectize\"></select>' +
                ' </div>' +
                '</div>' +

                '</div>');
            var jqueryFilterCountry = "#"+"filterCountry"+inputCountryFieldCounter.toString();
            var jqueryFilterCountryAndOr = "#"+"filterCountryAndOr"+inputCountryFieldCounter.toString();



            $(jqueryFilterCountry).change(function () {

                var selectionFilter = $(jqueryFilterCountry).val();

                var selectionFilterAndOr = $(jqueryFilterCountryAndOr).val();
                var resultSelectionFilter = selectionFilterAndOr + " " + selectionFilter;
                if ($("#filterQuery").val()==""){
                    $("#filterQuery").val(resultSelectionFilter) ;
                }else{
                    $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
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


        });


        $('#buttonIsActive').on('click', function () {

            $("#inputIsActive").empty();
            $("#inputIsActive").append('<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterisActive\">Seleziona se Utenti Iscritti </label><select id=\"filterisActive\" name=\"filterisActive\" class=\"full-width selectpicker\" placeholder=\"Selezione se Iscritti"' +
                'data-init-plugin=\"selectize\">' +
                '<option value=\"\"></option>' +
                '<option value=\"and nu.isActive=1\">ISCRITTI</option>' +
                '<option value=\"and nu.isActive=\'0\'\">NON ISCRITTI</option>' +
                '</select>' +
                ' </div>' +
                '</div>' +
                '</div>');
            $("#filterisActive").change(function () {

                var selectionFilter =  $("#filterisActive").val();


                var resultSelectionFilter =  " " + selectionFilter ;
                if ($("#filterQuery").val()==""){
                    $("#filterQuery").val(resultSelectionFilter) ;
                }else{
                    $('#filterQuery').val($('#filterQuery').val() + " " + resultSelectionFilter);
                }


            });

        });

        $('#buttonOrder').on('click', function () {


            $("#inputQuery").append('<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterOrderDateStart\">Seleziona Esclusione Ordini da Data </label><input type="\date\" id=\"filterOrderDateStart\" name=\"filterOrderDateStart\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini da Data "' +
                'value=\"\"/>' +
                ' </div>' +
                '</div>' +
                '</div>');
            $("#inputQuery").append('<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"filterOrderDateFinish\">Seleziona Esclusione Ordini a Data </label><input type="\date\" id=\"filterOrderDateFinish\" name=\"filterOrderDateFinish\" class=\"form-control\" placeholder=\"Seleziona Esclusione Ordini a Data "' +
                'value=\"\"/>' +
                ' </div>' +
                '</div>' +
                '</div>');

        });

        $('#buttonClean').on('click', function () {
            $("#inputQuery").empty();
            $("#inputOrderDate").empty();
            $("#inputIsActive").empty();
            $("#inputCity").empty();
            $("#inputCountry").empty();
            $("#inputAge").empty();
            $("#inputGender").empty();
            $("#filterAge").val("");
            $("#filterCity").val("");
            $("#filterCountry").val("");
            $("#filterQuery").val("");
            $("#filterQuery").empty();
            inputAgeFieldCounter =0;
            inputCountryFieldCounter = 0;
            inputCityFieldCounter = 0;
            inputGenderFieldCounter = 0;
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
    let bsModal = new $.bsModal('Salva il Segmento di Pubblico', {
        body: '<div><p>Premere ok per Salvare ' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let filterQuery = $('#filterQuery').val();
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
        if (typeof filterQuery === "undefined") {
            filterCountry = "";
        } else {
            filterQuery = filterQuery ;
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
            code: $('#code').val(),
            sql: filterGender + ' ' + filterAge + ' ' + filterCity + ' ' + filterCountry + ' ' + filterQuery + ' ' + filterIsActive + ' ' + filterOrderDateExclude + groupby,
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




