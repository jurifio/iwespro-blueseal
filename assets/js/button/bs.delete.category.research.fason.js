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
        <div>
            <div class="col-md-4 pre-scrollable text-left">
                 <p>Seleziona i generi da eliminare</p>
                 <div id="checkGender"></div>
            </div>
            <div class="col-md-4 pre-scrollable text-left">
                 <p>Seleziona le da eliminare</p>
                 <div id="checkCatGroup"></div>
            </div>
            <div class="col-md-4 pre-scrollable text-left">
                 <p>Seleziona i materiali da eliminare</p>
                 <div id="checkMaterial"></div>
            </div>
        </div>
        `
    });


    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeGender',
        }
    }).done(function(genders){
        $.each(genders, function (k, v) {
            $('#checkGender').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + '</div>');
        })
    });

    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeCategoryGroup',
        }
    }).done(function(catGroup){
        $.each(catGroup, function (k, v) {
            $('#checkCatGroup').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + '</div>');
        })
    });

    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeMaterial',
        }
    }).done(function(material){
        $.each(material, function (k, v) {
            $('#checkMaterial').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + '</div>');
        })
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        let checkGender = [];
        $('#checkGender input:checked').each(function(i){
            checkGender[i] = $(this).attr('value');
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