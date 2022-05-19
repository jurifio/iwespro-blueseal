window.buttonSetup = {
    tag:"a",
    icon:"fa-address-book",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-main-label",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci etichette",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-main-label', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare ALMENO una riga"
        }).open();
        return false;
    }

    let products = [];
    let prototypes = [];

    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
        prototypes.push(v.pspRow_Id);
    });

    let uniquePrototype = [...new Set(prototypes)];

    if(uniquePrototype.length != 1) {
        new Alert({
            type: "warning",
            message: "Aggiorna etichette di gruppi omogenei"
        }).open();
        return false;
    }


    const dataGet = {
      prototypeId: uniquePrototype[0]
    };
    $.ajax({
        method: 'GET',
        data: dataGet,
        url: '/blueseal/xhr/FrontMainLabelManageAjaxController',
        dataType: "json"
    }).done(function (res) {
        $.each(res, function (k, v) {
            $('.labelContainer').append(
                `
                <div class="labelValue">
                    <label for="${v.id}">${v.name}</label>
                    <select id="${v.id}"></select>
                </div>
                `
            )
        })
    });


    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductDetailTranslation',
            condition: {
                langId: 1
            }
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('.labelValue select');
        if (select.length > 0 && typeof select[0].selectize != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'productDetailId',
            labelField: 'name',
            searchField: ['name'],
            options: res2,
        });
        select[0].selectize.setValue(1);
    });


    let bsModal = new $.bsModal('Setta il valore delle etichette', {
        body: `<div class="labelContainer">
               </div>`
    });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let labelValue = [];

            $('.labelValue select').each(function () {
                labelValue.push({
                    [$(this).attr('id')]: $(this).val()
                })
            });

            const data = {
                products: products,
                labelValue: labelValue
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/FrontMainLabelManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

});