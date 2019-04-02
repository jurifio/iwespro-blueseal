window.buttonSetup = {
    tag:"a",
    icon:"fa-flag",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tag-new-brand",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa massivamente il tag 'New Brand'",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-tag-new-brand', function () {

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductBrand'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#brand');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res,
        });
    });


    $.ajax({
        method: 'get',
        url: '/blueseal/xhr/SpecialTagsManageAjaxController',
        dataType: 'json'
    }).done(function (res2) {
        var specialTags = $('#special-tags');
        if(typeof (specialTags[0].selectize) != 'undefined') specialTags[0].selectize.destroy();
        specialTags.selectize({
            valueField: 'id',
            labelField: 'trName',
            searchField: 'trName',
            options: res2,
        });
    });

    let bsModal = new $.bsModal("Seleziona i brand e associali ad uno special tag", {
        body: `<p>Seleziona il brand a cui associare la nuova etichetta</p>
                <div>
                <p id="actualSelectedBrand">Le stagioni attualmente utilizzate sono: </p>
                </div>
                <select id="brand">
                <option disabled selected value>Seleziona un brand</option>
                </select>
                <p>Seleziona il TAG Speciale</p>
                <select id="special-tags">
                <option disabled selected value>Seleziona tag speciale</option>
                </select>
                <p>Operazione da effettuare:</p>
                <select id="type">
                <option disabled selected value>Seleziona un\'opzione</option>
                <option id="add" value="add">Aggiungi</option>
                <option id="del" value="del">Rimuovi</option>
                </select>
                <div id="operation">
                </div>`
    });

    $('#special-tags').change(function () {
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/SpecialNewBrandTagsAjaxController',
            data: {
                tagId: $(this).val()
            },
            dataType: 'json'
        }).done(function (brandsNames) {
            $.each(brandsNames, function (k, v) {
                $('#actualSelectedBrand').append(`<strong>${v}</strong> | `);
            })
        });
    });

    $('#type').change(function() {
        if($('#type option:selected').val() == 'add') {
            $('#operation').append(
                `<p>Seleziona la posizione</p>
            <select id="selectPos"></select>    
            `
            );

            $.ajax({
                url: '/blueseal/xhr/getTableContent',
                method: 'GET',
                data: {
                    table: 'TagPosition',
                },
                dataType: 'json'
            }).done(function (resPosition) {
                var tagPosition = $('#selectPos');
                if(typeof (tagPosition[0].selectize) != 'undefined') tagPosition[0].selectize.destroy();
                tagPosition.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: resPosition,
                });
            });
        } else {
            $('#operation').empty()
        }
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let type = $('#type').val();
        let pos = $('#selectPos').val();

        if(type === 'add'){
            const data = {
                brand: $('#brand').val(),
                tag: $('#special-tags').val(),
                pos: pos
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/SpecialNewBrandTagsAjaxController',
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
                brand: $('#brand').val(),
                tag: $('#special-tags').val()
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/SpecialNewBrandTagsAjaxController',
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