window.buttonSetup = {
    tag:"a",
    icon:"fa-unlock",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-marketplace-enable",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Abilita prodotto sui marketplace",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-marketplace-enable', function (e, element, button) {

    let bsModal = $('#bsModal');
    let header = $('.modal-header h4');
    let body = $('.modal-body');
    let cancelButton = $('.modal-footer .btn-default');
    let okButton = $('.modal-footer .btn-success');

    header.html('Abilita Prodotti');

    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;
    var activeAutomatic='';
    var automaticText='';

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });


    body.html('<img src="/assets/img/ajax-loader.gif" />');
    okButton.hide();

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductPrestashopManageController',
            type: "get"
        }).done(function (response) {
            okButton.show();
            let accounts = JSON.parse(response);
            let html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="marketPlaceId">Marketplace Account</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="marketPlaceId" id="marketPlaceId" required>' +
                '<option value=""></option>';
            for(let account of accounts) {
                html+='<option value="'+account.id+'">'+account.shopname+' - '+account.name+'</option>';
            }
            html+='</select>';
            html+='</div>';
            html+='<div class="form-group form-group-default selectize-enabled full-width">';
            html+= '<label for="marketPlaceId">Azione</label><select class="full-width" placeholder="Abilita" data-init-plugin="selectize" title="" name="enableProd" id="enableProd" required>';
            html+='<option value=""></option><option value="1">Abilita</option><option value="0">Disabilita</option></select></div>';
            html+='<div id="pubblicazione"></div>';


            body.html($(html));

            Pace.ignore(function () {
                okButton.html('Esegui').off().on('click',function () {
                    okButton.off().hide().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        marketplaceHasShopId: $('#marketPlaceId').val(),
                        action: $('#enableProd').val(),
                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/PrestashopActivateProductManage',
                        type: "POST",
                        data: data
                    }).done(function () {
                        body.html('Richiesta di Abilitazione inviata');
                    }).fail(function () {
                        body.html('Errore imprevisto');
                    }).always(function () {
                        okButton.html('Chiudi').show();
                    })
                });
            });
        });
    });

    bsModal.modal();
});

$(document).on('change','#accountId',function() {
    //window.x = $(this);

        $('#modifier').val($(this).find(':selected').data('modifier'));
        $('#cpc').val($(this).find(':selected').data('cpc'));
        if($(this).find(':selected').data('activeautomatic')=='1'){
           automaticText='Si';
            activeAutomatic='1';
        }else{
            automaticText='No';
            activeAutomatic='0';
        }
        $('#pubblicazione').empty();
        $('#pubblicazione').append(automaticText);
        if ($(this).find(':selected').data('hasCpc')) {
            $("#cpc").parent().show();
        } else {
            $("#cpc").parent().hide();
        }


});
