$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'ProductBrand',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#brandId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });

});
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'ProductSeason',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#seasonId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });

});



var typeAssignBrand;
$(':radio[name="typeAssignBrand"]').change(function(){
    typeAssignBrand=this.value;
    if(typeAssignBrand=='2'){
        $('#rawRuleBrand').removeClass('hide');
        $('#rawRuleBrand').addClass('show');
        $('#brandsPar').val('');

    } else{
        $('#rawRuleBrand').removeClass('show');
        $('#rawRuleBrand').addClass('hide');
        $('#brandsPar').value='0';
    }
});
var typeAssignSeason;
$(':radio[name="typeAssignSeason"]').change(function(){
    typeAssignSeason=this.value;
    if(typeAssignSeason=='2'){
        $('#rawRuleSeason').removeClass('hide');
        $('#rawRuleSeason').addClass('show');
        $('#seasonsPar').val('');

    } else{
        $('#rawRuleSeason').removeClass('show');
        $('#rawRuleSeason').addClass('hide');
        $('#seasonsPar').value='0';
    }
});
var valueBrand='';
var newValueBrand='';
$('#brandId').change( function(){
    newValueBrand=valueBrand+this.value+',';
    if( $('#brandsPar').val(newValueBrand)==null) {
        valueBrand=''
    }else{
        valueBrand=$('#brandsPar').val();
    }


    $('#brandsPar').val(newValueBrand);
    $('#appendBrandsPar').append(`
    <div id="brandAddDiv-`+$('#brandId').val()+`" class="row"><div class="col-md-12">`+$('#brandId :selected').text()+`</div><div class="col-md-2"> <button class="success" id="btnAdd-`+$('#brandId').val()+`" onclick="lessBrandAdd(`+$('#brandId').val()+`)" type="button"><span  class="fa fa-close"></span></button></div></div>`);

});

var valueSeason='';
var newValueSeason='';
$('#seasonId').change( function(){
    if($('#seasonsPar').val()==null){
        valueSeason='';
    }else{
        valueSeason=$('#seasonsPar').val();
    }
    newValueSeason=valueSeason+this.value+',';
    $('#seasonsPar').val(newValueSeason);
    $('#appendSeasonsPar').append(`
    <div id="seasonsAddDiv-` + $('#seasonsPar').val() + `" class="row"><div class="col-md-12">` + $('#seasonId :selected').text() + `</div><div class="col-md-2"> <button class="success" id="seasonAdd-` + $('#seasonId').val() + `" onclick="lessSeasonAdd(` + $('#seasonId').val() + `)" type="button"><span  class="fa fa-close"></span></button></div></div>`);

});


$(document).on('bs.price.rule.add', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiungi Regola Listino');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();



    $.ajax({
        type: "POST",
        url: "#",
        data: $('form').serialize()
    }).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
        okButton.off().on('click', function() {
            bsModal.modal('hide');
            okButton.off();
            window.location.href = '/blueseal/listini/modifica-regola/'+content;
        });
    }).fail(function(){
        body.html('Errore grave');
        bsModal.modal();
    });
});
function lessBrandAdd(brandId) {
    var divToErase = '#brandAddDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandsPar').val();
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    var  newValueToWrite='';
    if(newValueToChange.length==1){
        newValueToWrite = '';
    }else {
        for (var i = 0; i < newValueToChange.length; i++) {
            if (newValueToChange[i] == valueToDelete) {
                newValueToChange.splice(i, 1);
            }
        }
        newValueToWrite = newValueToChange.toString();
    }

    if (newValueToWrite == '') {
        $('#brandsPar').val(newValueToWrite);
    } else {
        $('#brandsPar').val(newValueToWrite + ',');
    }
    $(divToErase).empty();
}
function lessSeasonAdd(seasonId) {
    var divToErase = '#seasonsAddDiv-' + seasonId;
    var valueToDelete = seasonId;
    var valueToChange = $('#seasonsPar').val();
    var newValueToChange = [];
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#seasonsPar').val(newValueToChange);
    }else{
        $('#seasonsPar').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}



