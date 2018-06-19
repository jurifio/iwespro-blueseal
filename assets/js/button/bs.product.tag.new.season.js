window.buttonSetup = {
    tag:"a",
    icon:"fa-sun-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tag-new-season",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa massivamente il tag 'New Season'",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-tag-new-season', function () {

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSeason',
            condition:{
                isActive:1
            }
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#season');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res,
        });
    });

    let bsModal = new $.bsModal("Seleziona la nuova stagione da marchiare come 'New Season'", {
        body: `<p>Seleziona la stagione da impostare/eliminare come "New Season"</p>
                <small>(Il processo potrebbe richieder un po di tempo, non toccare nulla fino al messaggio di avvenuto inserimento)</small>
                <select id="season">
                <option disabled selected value>Seleziona una stagione</option>
                </select>
                <p>Operazione da effettuare:</p>
                 <select id="type">
                <option disabled selected value>Seleziona un'\opzione</option>
                <option id="add" value="add">Aggiungi</option>
                <option id="del" value="del">Rimuovi</option>
                </select>
                `
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let type = $('#type').val();

        if(type === 'add') {
            const data = {
                season: $('#season').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductNewSeasonAjaxController',
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
                season: $('#season').val()
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductNewSeasonAjaxController',
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