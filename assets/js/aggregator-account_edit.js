$(document).ready(function () {

    document.getElementById('insertAggregator').style.display = "block";
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
   /* $.ajax({
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
            options: res2,
        });

    });*/

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Lang',
            condition: {isActive: 1}
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

    });
    var selectCampaignId=$('#selectCampaignId').val();
    if(selectCampaignId!='') {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var selectCampaign = $('#campaignName');
            if (typeof (selectCampaign[0].selectize) != 'undefined') selectCampaign[0].selectize.destroy();
            selectCampaign.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
                onInitialize: function () {
                    var selectize = this;
                    selectize.setValue(selectCampaignId);
                }

            });
        });
    }else{
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var selectCampaign = $('#campaignName');
            if (typeof (selectCampaign[0].selectize) != 'undefined') selectCampaign[0].selectize.destroy();
            selectCampaign.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });
        });
    }



});





$('#aggregatorHasShopId').change(function () {
    $('#rawBrands').empty();
    var shopSelect = $('#aggregatorHasShopId').val();
    $.ajax({
        url: '/blueseal/xhr/SelectBrandMarketplaceAccountAjaxController',
        method: 'get',
        data: {
            shop: shopSelect
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawBrands = res;
        let bodyres;
        bodyres='<div class="row"><div class="col-md-4"><input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"></div>';
        bodyres= bodyres+'<div class="col-md-4"><input type="text" id="myShop" onkeyup="myShopFunction()" placeholder="ricerca per Shop"></div>';
        bodyres= bodyres+'<div class="col-md-4"><input type="checkbox" class="form-control"  id="checkedAll" name="checkedAll"></div></div>';
        bodyres = bodyres+'<table id="myTable"> <tr class="header1"><th style="width:40%;">Categoria</th><th style="width:40%;">Shop</th><th style="width:20%;">Selezione</th></tr>';
        //  $('#rawBrands').append('<input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"/>');
        // $('#rawBrands').append('<table id="myTable"> <tr class="header1"><th style="width:20%;">Categoria</th><th style="width:20%;">Shop</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Destinazione</th><th style="width:20%;">Pubblicato</th></tr>');
        ////  $('#rawBrands').append('<div class="row"><div class="col-md-2">id Categoria</div><div class="col-md-2">Categoria</div><div class="col-md-2">ShopName</div>' + '<div class="col-md-2">Shop Id Origine</div>' + '<div class="col-md-2">Shop Id Destinazione</div><div class="col-md-2">Pubblicato</div></div>');
        $.each(rawBrands, function (k, v) {
            bodyres=bodyres+'<tr><td style="width:40%;">' + v.brandName + '</td><td style="width:40%;">' + v.shopName + '</td><td style="width:20%;"><input type="checkbox" class="form-control"  name="selected_values[]" value="'+v.id+'-'+v.shopIdOrigin+'"></td></tr>';
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        bodyres=bodyres+'</table>';
        $('#rawBrands').append(bodyres);
    });




});







$(document).on('bs.aggregator-account.save', function () {
    let bsModal = new $.bsModal('Inserimento Aggregatore', {
        body: '<p>Confermare?</p>'
    });

    var val='';
    $(':checkbox:checked').each(function(i){
        if($(this)!=$('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var ruleOption='';
    var lang = $('#lang').val();
    var marketplace_account_name = $('#marketplace_account_name').val();
    var slug = $('#slug').val();
    var aggregatorHasShopId = $('#aggregatorHasShopId').val();
    var nameAdminister = $('#nameAdminister').val();
    var marketplaceId = $('#marketplaceId').val();
    var marketplaceAccountId = $('#marketplaceAccountId').val();
    var emailNotify = $('#emailNotify').val();
    var isActive = $('#isActive').val();
    var logoFile = $('#logoFile').val();
    var campaignName=$('#campaignName').val();
    var typeInsertion=$('#typeInsertion').val();
    var typeInsertionCampaign=$('#typeInsertionCampaign').val();
    var nameRule = $('#nameRule').val();
    var rule=$('#ruleOption').val();

    if(val==''){
         ruleOption=rule;
    }else{
         ruleOption=val.substring(0, val.length - 1);
    }

    var config = '?nameAggregator=' + marketplace_account_name + '&' +
        'typeInsertion=' + typeInsertion + '&' +
        'campaignName=' + campaignName + '&' +
        'marketplaceId='+ marketplaceId + '&' +
        'lang=' + lang + '&' +
        'aggregatorHasShopId=' + aggregatorHasShopId + '&' +
        'marketplaceAccountId='+ marketplaceAccountId + '&' +
        'slug=' + slug + '&' +
        'logoFile=' + logoFile + '&' +
        'isActive=' + isActive + '&' +
        'nameAdminister=' + nameAdminister + '&' +
        'emailNotify=' + emailNotify + '&' +
        'nameRule='+nameRule+ '&' +
        'ruleOption='+ruleOption;



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = { marketplaceId:$('#marketplaceId').val(), marketplaceAccountId: $('#marketplaceAccountId').val()

        };
        var urldef = "/blueseal/xhr/AggregatorAccountInsertManage" + config;
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

