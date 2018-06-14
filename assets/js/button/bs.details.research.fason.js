window.buttonSetup = {
    tag:"a",
    icon:"fa-plus",
    permission:"/admin/product/edit&&allShops",
    event:"bs-new-batch-product-add",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi dettagli per riceca fason",
    placement:"bottom",
    toggle:"modal"
};


$(document).on('bs-new-batch-product-add', function () {


    let bsModal = new $.bsModal('Crea nuovi dettagli per la ricerca fason', {
        body: `
        <div>
            <div>
                 <p>Inserisci uno o più generi separati da ", "</p>
                 <textarea style="width: 400px; height: 150px" id="gender"></textarea>
            </div>
            <div>
                 <p>Inserisci una o più macrocategorie separate da ", "</p>
                 <textarea style="width: 400px; height: 150px" id="macro-cat"></textarea>
            </div>
            <div>
                 <p>Inserisci una o più categorie separati da ", "</p>
                 <textarea style="width: 400px; height: 150px" id="cat"></textarea>
            </div>
            <div>
                 <p>Inserisci uno o più materiali separati da ", "</p>
                 <textarea style="width: 400px; height: 150px" id="material"></textarea>
            </div>
        </div>
        `
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            gender: $('#gender').val(),
            macroCat: $('#macro-cat').val(),
            cat: $('#cat').val(),
            material: $('#material').val()
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductSheetModelPrototypeForFason',
            data: data
        }).done(function (res) {
            bsModal.writeBody('Dettagli inseriti con successo');
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