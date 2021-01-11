$(document).ready(function () {


    document.getElementById('insertMarketPlaceAccount').style.display = "block";
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
            table: 'Marketplace',

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
        select[0].selectize.setValue($('#shopSelectId').val());
    });



$(document).on('bs.marketplaceaccountshop-account.save', function () {
    let bsModal = new $.bsModal('Modifica Marketplace Account', {
        body: '<p>Confermare?</p>'
    });

    var val = '';
    $(':checkbox:checked').each(function (i) {
        if ($(this) != $('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var marketplaceId = $('#marketplaceId').val();
    var marketplace_account_name = $('#marketplace_account_name').val();
    var shopId = $('#shopId').val();
    var marketplaceHasShopId = $('#marketplaceHasShopId').val();

    var isActive = $('#isActive').val();
    var logoFile = $('#logoFile').val();



    var config = '?nameMarketPlace=' + marketplace_account_name + '&' +
        'marketplaceId=' + marketplaceId + '&' +
        'marketplaceHasShopId=' + marketplaceHasShopId + '&' +
        'shopId=' + shopId + '&' +
        'logoFile=' + logoFile + '&' +
        'isActive=' + isActive;


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/MarketplaceAccountShopInsertManage" + config;
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

$(document).on('click', '#checkedAll', function () {

    $('input:checkbox').not(this).prop('checked', this.checked);

});

function lessBrandAdd(brandId) {
    var divToErase = '#brandAddDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandis').val();
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandis').val(newValueToChange);
    } else {
        $('#brandis').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}

function lessBrand(brandId) {
    var divToErase = '#brandDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandis').val();
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {
        if (newValueToChange[i] == valueToDelete) {
            newValueToChange.splice(i, 1);
        }
    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandis').val(newValueToChange);
    } else {
        $('#brandis').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}

function lessBrandParallelAdd(brandId) {
    var divToErase = '#brandParallelAddDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandsPar').val();
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandsPar').val(newValueToChange);
    }else{
        $('#brandsPar').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}

function lessBrandParallel(brandId) {
    var divToErase = '#brandParallelDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandsPar').val();
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandsPar').val(newValueToChange);
    }else{
        $('#brandsPar').val(newValueToChange + ',');
    }
    $(divToErase).empty();
}

function lessBrandSaleAdd(brandId) {
    var divToErase = '#brandSaleAddDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandSaleExclusion').val();
    var strlen = valueToChange.length - 1;
    valueToChange = valueToChange.substr(0, strlen);
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandSaleExclusion').val(newValueToChange);
    } else {
        $('#brandSaleExclusion').val(newValueToChange + ',');
    }


    $(divToErase).empty();
}

function lessBrandSale(brandId) {
    var divToErase = '#brandSaleDiv-' + brandId;
    var valueToDelete = brandId;
    var valueToChange = $('#brandSaleExclusion').val();
    var newValueToChange = [];
    newValueToChange = valueToChange.split(',');
    for (var i = 0; i < newValueToChange.length; i++) {

        if (newValueToChange[i] == valueToDelete) {

            newValueToChange.splice(i, 1);
        }

    }
    newValueToChange = newValueToChange.toString();
    if (newValueToChange == '') {
        $('#brandSaleExclusion').val(newValueToChange);
    } else {
        $('#brandSaleExclusion').val(newValueToChange + ',');
    }

    $(divToErase).empty();
}

