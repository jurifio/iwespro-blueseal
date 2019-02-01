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
        url: '/blueseal/xhr/ProductNewSeasonAjaxController',
        dataType: 'json'
    }).done(function (seasonsNames) {
        $.each(seasonsNames, function (k, v) {
            $('#actualSelectedSeason').append(`<strong>${v}</strong> | `);
        })
    });

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

    let bsModal = new $.bsModal("Seleziona la nuova stagione da marchiare come 'New Season'", {
        body: `<p>Seleziona la stagione da impostare/eliminare come "New Season"</p>
                <div>
                <p id="actualSelectedSeason">Le stagioni attualmente utilizzate sono: </p>
                </div>
                <small>(Il processo potrebbe richieder un po di tempo, non toccare nulla fino al messaggio di avvenuto inserimento)</small>
                <select id="season">
                <option disabled selected value>Seleziona una stagione</option>
                </select>
                <p>Seleziona il TAG Speciale</p>
                <select id="special-tags">
                <option disabled selected value>Seleziona tag speciale</option>
                </select>
                <p>Operazione da effettuare:</p>
                 <select id="type">
                <option disabled selected value>Seleziona un'\opzione</option>
                <option id="add" value="add">Aggiungi</option>
                <option id="del" value="del">Rimuovi</option>
                </select>
                <div id="operation">
                </div>`
    });

    $('#special-tags').change(function () {
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/ProductNewSeasonAjaxController',
            data: {
                tagId: $(this).val()
            },
            dataType: 'json'
        }).done(function (seasonsNames) {
            $.each(seasonsNames, function (k, v) {
                $('#actualSelectedSeason').append(`<strong>${v}</strong> | `);
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

        if(type === 'add') {
            const data = {
                season: $('#season').val(),
                tag: $('#special-tags').val(),
                pos: pos
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
                season: $('#season').val(),
                tag: $('#special-tags').val()
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