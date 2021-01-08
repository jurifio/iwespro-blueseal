$(document).ready(function () {

    document.getElementById('insertShareShop').style.display = "block";
    $('#uploadLogo').click(function () {
        let bsModal = $('#bsModal');

        let header = bsModal.find('.modal-header h4');
        let body = bsModal.find('.modal-body');
        let cancelButton = bsModal.find('.modal-footer .btn-default');
        let okButton = bsModal.find('.modal-footer .btn-success');

        bsModal.modal();

        header.html('Carica Foto');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();
        let bodyContent =
            '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">' +
            '<div class="fallback">' +
            '<input name="file" type="file" multiple />' +
            '</div>' +
            '</form>';

        body.html(bodyContent);
        let dropzone = new Dropzone("#dropzoneModal", {
            url: "/blueseal/xhr/UploadAggregatorImageAjaxController",
            maxFilesize: 5,
            maxFiles: 100,
            parallelUploads: 10,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
            },
            success: function (res) {
                $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name'] + '">');
                $('#logoFile').val('https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name']);
            }
        });

        dropzone.on('addedfile', function () {
            okButton.attr("disabled", "disabled");
        });
        dropzone.on('queuecomplete', function () {
            okButton.removeAttr("disabled");
            $(document).trigger('bs.load.photo');
        });
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            condition: {hasEcommerce: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2
        });
        select[0].selectize.setValue($('#shopSelected').val());
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Marketplace',
            condition: {type: "website"}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#marketplaceId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
        select[0].selectize.setValue($('#marketplaceSelectedId').val());
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductStatus'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productStatusId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
        select[0].selectize.setValue($('#productStatusIdSelected').val());
    });


    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Lang',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#lang');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'lang',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
        select[0].selectize.setValue($('#langSelectId').val());
    });
    $('#shopId').change(function(){
        $.ajax({
            url: '/blueseal/xhr/SelectShopBrandsAjaxController',
            method: 'get',
            data: {
                shopId:$('#shopId').val()
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let brandId = $('#brandId');
            if (typeof (brandId[0].selectize) != 'undefined') brandId[0].selectize.destroy();
            brandId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">Marketplace:' + escape(item.hasMarketplaceRights) + ' | Aggregatori:' + escape(item.hasAggregator) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">Marketplace:' + escape(item.hasMarketplaceRights) + ' | Aggregatori:' + escape(item.hasAggregator) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            url: '/blueseal/xhr/SelectOtherShopBrandsAjaxController',
            method: 'get',
            data: {
                shopId:$('#shopId').val()
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let BrandIdParallel = $('#BrandIdParallel');
            if (typeof (BrandIdParallel[0].selectize) != 'undefined') BrandIdParallel[0].selectize.destroy();
            BrandIdParallel.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">Marketplace:' + escape(item.hasMarketplaceRights) + ' | Aggregatori:' + escape(item.hasAggregator) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">Marketplace:' + escape(item.hasMarketplaceRights) + ' | Aggregatori:' + escape(item.hasAggregator) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

    });



});
var SelectionFullPrice;
$(':radio[name="activeFullPrice"]').change(function(){
    SelectionFullPrice=this.value;
   if(SelectionFullPrice=='2'){
       $('#rawFullPrice').removeClass('hide');
       $('#rawFullPrice').addClass('show');
   } else{
       $('#rawFullPrice').removeClass('show');
       $('#rawFullPrice').addClass('hide');
       $('#appendBrand').empty();
       $('#appendBrand').append('tutti');
   }
});
var Selection;
$(':radio[name="activeSalePrice"]').change(function(){
    var brandExclusion='';
    Selection=this.value;
    if(Selection=='2'){
        $('#rawSalePrice').removeClass('hide');
        $('#rawSalePrice').addClass('show');
        brandExclusion='';
        $('#brandSaleExclusion').val(brandExclusion)
        $('#divBrandExclusion').empty();
    } else{
        $('#rawSalePrice').removeClass('show');
        $('#rawSalePrice').addClass('hide');
        brandExclusion='0';
        $('#brandSaleExclusion').val(brandExclusion)
        $('#divBrandExclusion').empty();
        $('#divBrandExclusion').append('Tutti');
    }
});
var SelectionNameCatalog;
$(':radio[name="checkNameCatalog"]').change(function(){
    SelectionNameCatalog=this.value;
    if(SelectionNameCatalog=='2'){
        $('#rawName').removeClass('hide');
        $('#rawName').addClass('show');
    } else{
        $('#rawName').removeClass('show');
        $('#rawName').addClass('hide');
    }
});
var typeAssign;
$(':radio[name="typeAssign"]').change(function(){
    typeAssign=this.value;
    if(typeAssign=='2'){
        $('#rawRule').removeClass('hide');
        $('#rawRule').addClass('show');
        $('#brands').val('');

    } else{
        $('#rawRule').removeClass('show');
        $('#rawRule').addClass('hide');
        $('#brands').value='0';
    }
});
var typeAssignParallel;
$(':radio[name="typeAssignParallel"]').change(function(){
    typeAssignParallel=this.value;
    if(typeAssignParallel=='2'){
        $('#rawRuleParallel').removeClass('hide');
        $('#rawRuleParallel').addClass('show');
        $('#brandsParallel').val('');

    } else{
        $('#rawRuleParallel').removeClass('show');
        $('#rawRuleParallel').addClass('hide');
        $('#brandsParallel').value='0';
    }
});
var valueBrand;
var newValueBrand;
$('#brandId').change( function(){
     valueBrand=$('#brands').val();
    newValueBrand=valueBrand+this.value+',';
    $('#brands').val(newValueBrand);
    $('#appendBrandsPublishPar').append(`
    <div id="brandAddDiv-`+$('#brandId').val()+`" class="row"><div class="col-md-12">`+$('#brandId :selected').text()+`</div><div class="col-md-2"> <button class="success" id="btnAdd-`+$('#brandId').val()+`" onclick="lessBrandAdd(`+$('#brandId').val()+`)" type="button"><span  class="fa fa-close"></span></button></div></div>`);

});



var paral;
var newparal;
$('#BrandIdParallel').change( function(){
    paral=$('#brandsPar').val();
    newparal=paral+this.value + ',';
    $('#brandsPar').val(newparal);
    $('#appendBrandsPar').append(`
    <div id="brandParallelAddDiv-`+$('#brandIdParallel').val()+`" class="row"><div class="col-md-12">`+$('#BrandIdParallel :selected').text()+`</div><div class="col-md-2"> <button class="success" id="btnParallelAdd-`+$('#BrandIdParallel').val()+`" onclick="lessBrandParallelAdd(`+$('#BrandIdParallel').val()+`)" type="button"><span  class="fa fa-close"></span></button></div></div>`);
});



$(document).on('bs.productsharehasshopdestination-account.save', function () {
    let bsModal = new $.bsModal('Modifica Regole Prodotti Paralleli', {
        body: '<p>Confermare?</p>'
    });

    var val='';
    $(':checkbox:checked').each(function(i){
        if($(this)!=$('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var lang = $('#lang').val();
    var marketplace_account_name = $('#marketplace_account_name').val();
    var marketplaceAccountId=$('#marketplaceAccountId').val();
    var slug = $('#slug').val();
    var shopId = $('#shopId').val();
    var marketplaceId =$('#marketplaceId').val();
    var nameAdminister = $('#nameAdminister').val();
    var emailNotify = $('#emailNotify').val();
    var isActive = $('#isActive').val();
    var isActiveShare = $('#isActiveShare').val();
    var isActivePublish = $('#isActivePublish').val();
    var logoFile = $('#logoFile').val();
    var productStatusId=$('#productStatusId').val();
    var typeAssign = $('#typeAssign').val();
    var brands = $('#brands').val();
    var typeAssignParallel = $('#typeAssignParallel').val();
    var brandsSelectionParallel = $('#brandsPar').val();
    if(brandsSelectionParallel==','){
        brandsSelectionParallel='0';
    }
    if(brands==','){
        brands='0';
    }




    var config = '?nameMarketPlace=' + marketplace_account_name + '&' +
        'lang=' + lang + '&' +
        'shopId=' + shopId + '&' +
        'marketplaceId=' + marketplaceId + '&' +
        'marketplaceAccountId=' + marketplaceAccountId + '&' +
        'slug=' + slug + '&' +
        'logoFile=' + logoFile + '&' +
        'isActive=' + isActive + '&' +
        'isActiveShare=' + isActiveShare + '&' +
        'isActivePublish=' + isActivePublish + '&' +
        'nameAdminister=' + nameAdminister + '&' +
        'emailNotify=' + emailNotify + '&' +
        'productStatusId=' + productStatusId + '&' +
        'typeAssign=' + typeAssign + '&' +
        'brands=' + brands+ '&' +
        'typeAssignParallel=' + typeAssignParallel + '&' +
        'brandsParallel=' + brandsSelectionParallel;

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/ProductShareHasShopDestinationInsertManage" + config;
        $.ajax({
            method: "PUT",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
            });
            bsModal.showOkBtn();
        });
    });
});

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function addRulesTab() {
    var brandId = $('#brandId').val();
    var shopSupplierId = $('#shopSupplierId').val();


}

function myFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}
function myShopFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myShop");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

$(document).on('click','#checkedAll',function () {

    $('input:checkbox').not(this).prop('checked', this.checked);

});

function lessBrandAdd(brandId){
    var divToErase='#brandAddDiv-'+brandId;
    var valueToDelete=brandId;
    var valueToChange=$('#brands').val();
    var strlen=valueToChange.length-1;
    valueToChange=valueToChange.substr(0,strlen);
    var newValueToChange=[];
    newValueToChange=valueToChange.split(',');
    for( var i = 0; i < newValueToChange.length; i++){

        if ( newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange=newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brands').val(newValueToChange);
    } else {
        $('#brands').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}
function lessBrand(brandId){
    var divToErase='#brandDiv-'+brandId;
    var valueToDelete=brandId;
    var valueToChange=$('#brands').val();
    var strlen=valueToChange.length-1;
    valueToChange=valueToChange.substr(0,strlen);
    var newValueToChange=[];
    newValueToChange=valueToChange.split(',');
    for( var i = 0; i < newValueToChange.length; i++){

        if ( newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange=newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brands').val(newValueToChange);
    } else {
        $('#brands').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}
function lessBrandParallelAdd(brandId){
    var divToErase='#brandParallelAddDiv-'+brandId;
    var valueToDelete=brandId;
    var valueToChange=$('#brandsPar').val();
    var strlen=valueToChange.length-1;
    valueToChange=valueToChange.substr(0,strlen);
    var newValueToChange=[];
    newValueToChange=valueToChange.split(',');
    for( var i = 0; i < newValueToChange.length; i++){

        if ( newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange=newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandsPar').val(newValueToChange);
    } else {
        $('#brandsPar').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}
function lessBrandParallel(brandId){
    var divToErase='#brandParallelDiv-'+brandId;
    var valueToDelete=brandId;
    var valueToChange=$('#brandsPar').val();
    var strlen=valueToChange.length-1;
    valueToChange=valueToChange.substr(0,strlen);
    var newValueToChange=[];
    newValueToChange=valueToChange.split(',');
    for( var i = 0; i < newValueToChange.length; i++){

        if ( newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange=newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandsPar').val(newValueToChange);
    } else {
        $('#brandsPar').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}

