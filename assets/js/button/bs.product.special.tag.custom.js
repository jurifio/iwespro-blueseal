window.buttonSetup = {
    tag:"a",
    icon:"fa-arrows-h",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tag-new-custom",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa massivamente un tag custom'",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-tag-new-custom', function () {


    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    let products = [];
    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
    });

    $.ajax({
        method: 'get',
        url: '/blueseal/xhr/SpecialTagsManageAjaxController',
        dataType: 'json'
    }).done(function (res2) {
        var specialTags = $('#specialtags');
        if(typeof (specialTags[0].selectize) != 'undefined') specialTags[0].selectize.destroy();
        specialTags.selectize({
            valueField: 'id',
            labelField: 'trName',
            searchField: 'trName',
            options: res2,
        });
    });

    let bsModal = new $.bsModal("Associa singoli prodotti ad una special tag", {
        body: `<p>Seleziona il TAG Speciale</p>
                <select id="specialtags">
                <option disabled selected value>Seleziona tag speciale</option>
                </select>
                <p>Operazione da effettuare:</p>
                <select id="typec">
                <option disabled selected value>Seleziona un\'opzione</option>
                <option id="add" value="add">Aggiungi</option>
                <option id="del" value="del">Rimuovi</option>
                </select>`
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let type = $('#typec').val();

        if(type === 'add'){
            const data = {
                p: products,
                tag: $('#specialtags').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/SpecialTagsManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        } else if(type === 'del'){
            const data = {
                p: products,
                tag: $('#specialtags').val()
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/SpecialTagsManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        }

    });
});