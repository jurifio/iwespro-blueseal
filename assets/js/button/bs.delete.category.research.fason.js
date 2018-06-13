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
                 <strong>GENERI</strong>
                 <div id="checkGender"></div>
            </div>
            <div class="col-md-4 pre-scrollable text-left">
                 <strong>MACROCATEGORIE</strong>
                 <div id="checkMacroCatGroup"></div>
            </div>
            <div class="col-md-4 pre-scrollable text-left">
                 <strong>CATEGORIE</strong>
                 <div id="checkCatGroup"></div>
            </div>
            <div class="col-md-4 pre-scrollable text-left">
                 <strong>MATERIALI</strong>
                 <div id="checkMaterial"></div>
            </div>
        </div>
        `
    });

    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');


    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeGender',
            orderBy: [
                'name'
            ]
        }
    }).done(function(genders){

        let numGend = 0;
        $.each(genders, function (k, v) {

            $.ajax({
                url: '/blueseal/xhr/getTableContent',
                method: 'GET',
                dataType: 'json',
                data: {
                    table: 'ProductSheetModelPrototype',
                    condition: {
                        genderId: v.id
                    }
                }
            }).done(function(ids){
                numGend = ids.length;
            }).success(function () {
                $('#checkGender').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + ' (' +  numGend +')</div>');
            });
        })
    });


    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeMacroCategoryGroup',
            orderBy: [
                'name'
            ]
        }
    }).done(function(macroCatGroup){


        let numMacroCat = 0;
        $.each(macroCatGroup, function (k, v) {

            $.ajax({
                url: '/blueseal/xhr/getTableContent',
                method: 'GET',
                dataType: 'json',
                data: {
                    table: 'ProductSheetModelPrototypeCategoryGroup',
                    condition: {
                        macroCategoryGroupId: v.id
                    }
                }
            }).done(function(ids){
                numMacroCat = ids.length;
            }).success(function () {
                $('#checkMacroCatGroup').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + ' (' + numMacroCat +')</div>');
            });
        });
    });




    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeCategoryGroup',
            orderBy: [
                'name'
            ]
        }
    }).done(function(catGroup){


        let numCat = 0;
        $.each(catGroup, function (k, v) {

            $.ajax({
                url: '/blueseal/xhr/getTableContent',
                method: 'GET',
                dataType: 'json',
                data: {
                    table: 'ProductSheetModelPrototype',
                    condition: {
                        categoryGroupId: v.id
                    }
                }
            }).done(function(ids){
                numCat = ids.length;
            }).success(function () {
                $('#checkCatGroup').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + ' (' + numCat +')</div>');
            });
        });
    });

    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {
            table: 'ProductSheetModelPrototypeMaterial',
            orderBy: [
                'name'
            ]
        }
    }).done(function(material){


        let numMat = 0;
        $.each(material, function (k, v) {

            $.ajax({
                url: '/blueseal/xhr/getTableContent',
                method: 'GET',
                dataType: 'json',
                data: {
                    table: 'ProductSheetModelPrototype',
                    condition: {
                        materialId: v.id
                    }
                }
            }).done(function(ids){
                numMat = ids.length;
            }).success(function () {
                $('#checkMaterial').append('<div><input type="checkbox" name="' + v.id + '" value="' + v.id + '" /> ' + v.name + ' (' + numMat +')</div>');
            });
        });
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