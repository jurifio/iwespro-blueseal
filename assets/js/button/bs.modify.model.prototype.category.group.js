window.buttonSetup = {
    tag:"a",
    icon:"fa-pencil-square-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-modify-product-sheet-category",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Modifica il nome della categoria",
    placement:"bottom",
    toggle:"modal"
};


$(document).on('bs-modify-product-sheet-category', function () {


    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSheetModelPrototypeCategoryGroup'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#categoryGroup');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });


    let bsModal = new $.bsModal('Crea nuovi dettagli per la ricerca fason', {
        body: `
        <div>
            <p>Seleziona la categoria</p>
            <select id="categoryGroup"></select>
            <p>Inserisci un nuovo nome</p>
            <input type="text" id="new-name">
        </div>
        
        `
    });



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            cat: $('#categoryGroup').val(),
            name: $('#new-name').val()
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ProductSheetModelPrototypeForFason',
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

});