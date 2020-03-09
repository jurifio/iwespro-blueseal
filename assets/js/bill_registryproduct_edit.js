$(document).ready(function () {
    var groupProductId;
    var groupUm;
    var groupCost;
    var groupPrice;
    var groupTaxes;
    var groupName;

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
            acceptedFiles: "image/jpeg",
            dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
            },
            success: function (res) {
                $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-productIwes/' + res['name'] + '">');
                $('#logoFile').val('https://iwes.s3.amazonaws.com/iwes-productIwes/' + res['name']);
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
            table: 'BillRegistryGroupProduct'


        },
        dataType: 'json'
    }).done(function (res2) {

        var select = $('#billRegistryGroupProductId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
            render: {
                item: function (item, escape) {
                    groupProductId=item.billRegistryCategoryProductId;
                    groupUm=item.um;
                    groupCost=parseFloat(item.cost).toFixed(2);
                    groupPrice=parseFloat(item.price).toFixed(2);
                    groupTaxes=item.billRegistryTypeTaxesId;
                    groupName=item.name;
                    return '<div>' +
                        '<span class="label">' + escape(item.codeProduct) + ' ' + escape(item.name) + '</span> - ' +
                        '<span class="caption">desc:' + escape(item.description) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    groupProductId=item.billRegistryCategoryProductId;
                    groupUm=item.um;
                    groupName=item.name;
                    groupCost=parseFloat(item.cost).toFixed(2);
                    groupPrice=parseFloat(item.price).toFixed(2);
                    groupTaxes=parseFloat(item.billRegistryTypeTaxesId).toFixed(2);
                    return '<div>' +
                        '<span class="label">' + escape(item.codeProduct) + ' ' + escape(item.name) + '</span> - ' +
                        '<span class="caption">desc:' + escape(item.description) + '</span>' +
                        '</div>'
                }
            }
        });


    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryTypeTaxes'


        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryTypeTaxesId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'description',
            searchField: 'description',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryCategoryProduct'


        },
        dataType: 'json'
    }).done(function (res2) {
        var selectCategoryProduct = $('#billRegistryCategoryProductId');
        if (typeof (selectCategoryProduct[0].selectize) != 'undefined') selectCategoryProduct[0].selectize.destroy();
        selectCategoryProduct.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });

    $('#billRegistryGroupProductId').change(function(){
        $('#billRegistryCategoryProductId').data('selectize').setValue(groupProductId);
        $('#um').val(groupUm);
        $('#price').val(groupPrice);
        $('#cost').val(groupPrice);
        $('#nameProduct').val(groupName);
        $('#billRegistryTypeTaxesId').data('selectize').setValue(groupTaxes);
    });

});



function addDescription(){

    var listDescription=$('#descriptionArray').val();
    var descriptionTemp=$('#descriptionTemp').val();
    listDescription=listDescription+$('#descriptionTemp').val()+',';
    $('#descriptionArray').val(listDescription);
    var bodyListDescription=`<div class="row">
                               <div class="col-md-12">`+descriptionTemp+`</div></div>`;
    $('#divDescription').append(bodyListDescription);
    document.getElementById('descriptionTemp').value = '';
}

$(document).on('bs.productIwes.save', function () {
    let bsModal = new $.bsModal('Inserimento Prodotto Iwes', {
        body: '<p>Confermare?</p>'
    });
    var val = $('#descriptionArray').val();
    var n;
    var descdet='#descdet';
    var i;
    var descdetf='';
    var o=parseInt($('#descdet').val());

    for(i=0;i=o;i++){
        descdetf=descdet+n;
        val+=$(descdetf).val()+',';
    }
    var config = '?billRegistryProductId'+$("#billRegistryProductId").val()+'&'+'' +
        'codeProduct=' + $("#codeProduct").val() + '&' +
        'nameProduct=' + $("#nameProduct").val() + '&' +
        'um=' + $("#um").val() + '&' +
        'logoFile=' + $("#logoFile").val() + '&' +
        'cost=' + $("#cost").val() + '&' +
        'price=' + $("#price").val() + '&' +
        'billRegistryGroupProductId=' + $("#billRegistryGroupProductId").val() + '&' +
        'billRegistryTypeTaxesId=' + $("#billRegistryTypeTaxesId").val() + '&' +
        'productList=' + val.substring(0, val.length - 1);


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/BillRegistryProductManageAjaxController" + config;
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
                bsModal.hide();
            });
            bsModal.showOkBtn();
            document.getElementById('um').value = '';
            document.getElementById('nameProduct').value = '';
            document.getElementById('cost').value = '';
            document.getElementById('price').value = '';
            document.getElementById('codeProduct').value = '';
            document.getElementById('billRegistryGroupProductId').value = '';
            document.getElementById('billRegistryTypeTaxesId').value = '';
            document.getElementById('descriptionTemp').value = '';
            $('#divDescription').empty();
        });
    });
});
function addGroupProduct(){
    let bsModal = new $.bsModal('Inserimento Gruppo Prodotti', {
        body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="groupCodeProduct">Codice Gruppo Prodotti</label>
                                        <input id="groupCodeProduct" autocomplete="off" type="text"
                                               class="form-control" name="groupCodeProduct"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="groupNameProduct">Nome Gruppo Prodotti</label>
                                        <input id="groupNameProduct" autocomplete="off" type="text"
                                               class="form-control" name="groupNameProduct" value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="groupBillRegistryCategoryProductId">Seleziona La Categoria </label>
                                        <select id="groupBillRegistryCategoryProductId" name="groupBillRegistryCategoryProductId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="groupBillRegistryTypeTaxesId">Seleziona L'aliquota </label>
                                        <select id="groupBillRegistryTypeTaxesId" name="groupBillRegistryTypeTaxesId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="groupIsActive">attivo</label>
                                        <input  type="checkbox" checked class="form-control"  id="groupIsActive" name="groupIsActive">
                                    </div>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="groupUm">Unita di misura</label>
                                        <input id="groupUm" autocomplete="off" type="text"
                                               class="form-control" name="groupUm"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="groupDescription">Descrizione</label>
                                        <input id="groupDescription" autocomplete="off" type="text"
                                               class="form-control" name="groupDescription" value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="groupProductType">Seleziona il tipo Gruppo Prodotto </label>
                                        <select id="groupProductType" name="groupProductType"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                                <option value="Service">Servizio</option>
                                                <option value="Product">Prodotto</option>
                                                <option value="Module">Modulo</option>
                                        </select>
                                        
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default">
                                        <label for="groupCost">Prezzo acquisto</label>
                                        <input id="groupCost" autocomplete="off" type="text"
                                               class="form-control" name="groupCost"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default">
                                        <label for="groupPrice">Prezzo Vendita</label>
                                        <input id="groupPrice" autocomplete="off" type="text"
                                               class="form-control" name="groupPrice"
                                               value=""
                                        />
                                    </div>
                                </div>
                                </div>`
                                });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryCategoryProduct'

        },
        dataType: 'json'
    }).done(function (res2) {
        var selectgroupBillRegistryCategoryProductId = $('#groupBillRegistryCategoryProductId');
        if (typeof (selectgroupBillRegistryCategoryProductId[0].selectize) != 'undefined') selectgroupBillRegistryCategoryProductId[0].selectize.destroy();
        selectgroupBillRegistryCategoryProductId.selectize({
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
            table: 'BillRegistryTypeTaxes'

        },
        dataType: 'json'
    }).done(function (res2) {
        var selectgroupBillRegistryTypeTaxesId = $('#groupBillRegistryTypeTaxesId');
        if (typeof (selectgroupBillRegistryTypeTaxesId[0].selectize) != 'undefined') selectgroupBillRegistryTypeTaxesId[0].selectize.destroy();
        selectgroupBillRegistryTypeTaxesId.selectize({
            valueField: 'id',
            labelField: 'description',
            searchField: 'description',
            options: res2,
        });

    });

    bsModal.showCancelBtn();
    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.setOkEvent(function () {
        var isActive;
        if ($('#groupIsActive').prop('checked',true)){
            isActive=1;

        }else{
            isActive=0;
        }
        const data = {

            codeProduct: $('#groupCodeProduct').val(),
            nameProduct: $('#groupNameProduct').val(),
            billRegistryCategoryProductId: $('#groupBillRegistryCategoryProductId').val(),
            billRegistryTypeTaxesId: $('#groupBillRegistryTypeTaxesId').val(),
            um: $('#groupUm').val(),
            description: $('#groupDescription').val(),
            price: $('#groupPrice').val(),
            cost: $('#groupCost').val(),
            productType: $('#groupProductType').val(),
            isActive:isActive

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryGroupProductManageAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });


}

function addCategoryProduct(){
    let bsModal = new $.bsModal('Inserimento Gruppo Prodotti', {
        body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="nameCategory">Categoria</label>
                                        <input id="nameCategory" autocomplete="off" type="text"
                                               class="form-control" name="nameCategory"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionCategory">Descrizione </label>
                                        <textarea id="descriptionCategory" autocomplete="off" type="text"
                                               class="form-control" name="descriptionCategory" value=""
                                        ></textarea>
                                    </div>
                                </div>
                              
                                </div>`
    });



    bsModal.showCancelBtn();
    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.setOkEvent(function () {

        const data = {

            nameCategory: $('#nameCategory').val(),
            descriptionCategory: $('#descriptionCategory').val(),


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryCategoryProductManageAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });


}



