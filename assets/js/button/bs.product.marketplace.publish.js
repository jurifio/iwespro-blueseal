window.buttonSetup = {
    tag:"a",
    icon:"fa-share",
    permission:"/admin/product/edit&&allShops",
    event:"bs.product.marketplace.publish",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Pubblica prodotto sui marketplace",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.product.marketplace.publish', function (e, element, button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Pubblica Prodotti');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

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

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductManageController',
            type: "get"
        }).done(function (response) {
            var accounts = JSON.parse(response);
            var html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="accountId">Marketplace Account</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="accountId" id="accountId" required>' +
                '<option value=""></option>';
            for(let account of accounts) {
                html+='<option value="'+account.id+'" data-has-cpc="'+account.cpc+'" data-modifier="'+account.modifier+'">'+account.marketplace+' - '+account.name+'</option>';
            }
            html+='</select>';
            html+='</div>';
            html+='<div class="form-group form-group-default"><label for="modifier">Modificatore</label><input id="modifier" type="text" value="0" aria-label="modifier"/></div>';
            html+='<div style="display:none" class="form-group form-group-default"><label for="cpc">CPC</label><input id="cpc" type="text" value="0" aria-label="modifier"/></div>';

            body.html($(html));

            Pace.ignore(function () {
                okButton.off().on('click',function () {
                    okButton.off().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        account: $('#accountId').val(),
                        modifier: $('#modifier').val(),
                        cpc: $('#cpc').val()
                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/MarketplaceProductManageController',
                        type: "POST",
                        data: data
                    }).done(function () {
                        body.html('Richiesta di pubblicazione inviata');
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
    if($(this).find(':selected').data('hasCpc')) {
        $("#cpc").parent().show();
    } else {
        $("#cpc").parent().hide();
    }
});
