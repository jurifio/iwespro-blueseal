window.buttonSetup = {
    tag:"a",
    icon:"fa-file-image-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-shooting-manage",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci shooting",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-shooting-manage', function (e, element, button) {

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

    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
    });

    let bsModal = new $.bsModal('Aggiungi prodotti in shooting', {
        body: '<p>Aggiugi prodotti in shooting</p>' +
        '<div class="form-group form-group-default required">' +
        '<label for="selectFriend">Seleziona il friend</label>' +
        '<select id="selectFriend" name="selectFriend"></select>' +
        '</div>' +
        '<div class="form-group form-group-default required">' +
        '<label for="friendDdt">DDT</label>' +
        '<input autocomplete="off" type="text" id="friendDdt" ' +
        'placeholder="DDT Friend" class="form-control" name="friendDdt" required="required">' +
        '</div>' +
        '<div class="form-group form-group-default required">' +
        '<label for="friendDdtNote">Note Ddt Friend</label>' +
        '<textarea id="friendDdtNote" name="friendDdtNote"></textarea>' +
        '</div>'
    });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $(selectFriend);
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            friendId: $('#selectFriend').val(),
            friendDdt: $('#friendDdt').val(),
            note: $('#friendDdtNote').val(),
            products: products
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductShootingAjaxController',
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
