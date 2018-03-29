window.buttonSetup = {
    tag:"a",
    icon:"fa-free-code-camp",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-force-shooting",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Forza inserimento prodotto nello shooting",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-force-shooting', function () {

    let products = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    let i = 0;
    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
        i++;
    });

    let bsModal = new $.bsModal('Aggiungi prodotti in shooting', {
        body: '<p>Forza prodotti su shooting</p>' +
        '<div class="form-group form-group-default required">' +
        '<label for="selectShooting">Seleziona lo shooting da forzare</label>' +
        '<select id="selectShooting" name="selectShooting"></select>' +
        '</div>'
    });


    const dataProducts = {
        products: products,
    };
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductShootingForceAjaxController',
        data: dataProducts
    }).done(function (res) {

        let products = JSON.parse(res);

        $.each(products, function(k, v) {
            $('#selectShooting') .append($("<option/>") .val(v) .text(v))
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            products: products,
            shooting: $('#selectShooting').val()
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ProductShootingForceAjaxController',
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