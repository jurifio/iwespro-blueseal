window.buttonSetup = {
    tag:"a",
    icon:"fa-certificate",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tofriendshop-statuschange",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Pubblica prodotto sui Ecommerce dei Friend",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-tofriendshop-statuschange', function (e, element, button) {

    let bsModal = $('#bsModal');
    let header = $('.modal-header h4');
    let body = $('.modal-body');
    let cancelButton = $('.modal-footer .btn-default');
    let okButton = $('.modal-footer .btn-success');

    header.html('Cambia Stato ai  Prodotti negli Shop dei Friend');

    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poter Aggiornare lo stato"
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
            html+='</div>'
            html+='<div class="form-group form-group-default">';
            html+='<label for=statusId>stato</label>';
            html+='<select id="statusId" name="statusId" class="full-width"></select>';
            html+='</div>';


            body.html($(html));

            Pace.ignore(function () {
                $.ajax({
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'ProductStatus'
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $('select[name=\"statusId\"]');
                    if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: res2,
                    });
                    select[0].selectize.setValue(1);
                });
                okButton.html('Esegui').off().on('click',function () {
                    okButton.off().hide().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        shop: $('#shopId').val(),
                        status:$('#statusId').val()
                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/ProductHasShopDestinationChangeStatusManageController',
                        type: "POST",
                        data: data
                    }).done(function () {
                        body.html('Richiesta di aggiornamento Stato inviata');
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
