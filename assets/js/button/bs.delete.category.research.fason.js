window.buttonSetup = {
    tag:"a",
    icon:"fa-times",
    permission:"/admin/product/edit&&allShops",
    event:"bs-del-cat-fason",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Rimuovi dettagli",
    placement:"bottom",
    toggle:"modal"
};


$(document).on('bs-del-cat-fason', function () {


    let bsModal = new $.bsModal('Elimina dettagli per la ricerca fason', {
        body: `
        <p>Procedere con l'eliminazione?</p>
        `
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        let checkGender = [];
        $('#checkGender input:checked').each(function(i){
            checkGender[i] = $(this).attr('value');
        });

        let checkMacroCatGroup = [];
        $('#checkMacroCatGroup input:checked').each(function(i){
            checkMacroCatGroup[i] = $(this).attr('value');
        });

        let checkCatGroup = [];
        $('#checkCatGroup input:checked').each(function(i){
            checkCatGroup[i] = $(this).attr('value');
        });

        let checkMaterial = [];
        $('#checkMaterial input:checked').each(function(i){
            checkMaterial[i] = $(this).attr('value');
        });


        const data = {
            gender: checkGender,
            macCat: checkMacroCatGroup,
            cat: checkCatGroup,
            material: checkMaterial
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/ProductSheetModelPrototypeForFason',
            data: data
        }).done(function (res) {
            let ris = '';
            let err = JSON.parse(res);
            $.each(err, function (k, v) {
                ris += `${v} <br>`
            });

            bsModal.writeBody(ris);
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