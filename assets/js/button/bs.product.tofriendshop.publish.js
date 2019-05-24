window.buttonSetup = {
    tag:"a",
    icon:"fa-exchange",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tofriendshop-publish",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Pubblica prodotto sui Ecommerce dei Friend",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-tofriendshop-publish', function (e, element, button) {

    let bsModal = $('#bsModal');
    let header = $('.modal-header h4');
    let body = $('.modal-body');
    let cancelButton = $('.modal-footer .btn-default');
    let okButton = $('.modal-footer .btn-success');

    header.html('Pubblica Prodotti');

    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli associare"
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
            url: '/blueseal/xhr/ProductHasShopDestinationManageController',
            type: "get"
        }).done(function (response) {
            okButton.show();
            let shops = JSON.parse(response);
            let html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="shopId">Shop Di Destinazione</label>' +
                '<select class="full-width" placeholder="Seleziona lo shop" ' +
                'data-init-plugin="selectize" title="" name="shopId" id="shopId" required>' +
                '<option value=""></option>';
            for(let shop of shops) {
                html+='<option value="'+shop.id+'" data-has-name="'+shop.name+'">'+shop.title+'</option>';
            }
            html+='</select>';
            html+='</div>';


            body.html($(html));

            Pace.ignore(function () {
                okButton.html('Esegui').off().on('click',function () {
                    okButton.off().hide().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        shop: $('#shopId').val()

                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/ProductHasShopDestinationManageController',
                        type: "POST",
                        data: data
                    }).done(function () {
                        body.html('Richiesta di pubblicazione inviata');
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
    if($(this).find(':selected').data('hasCpc')) {
        $("#cpc").parent().show();
    } else {
        $("#cpc").parent().hide();
    }
});
