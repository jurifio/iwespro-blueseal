$(document).on('bs.categoryTranslation.modify', function () {


    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Puoi  selezionare solo un record "
        }).open();
        return false;
    }
    var productCategoryId = selectedRows[0].DT_RowId;
    var shopId = selectedRows[0].shopId;
    var langId = selectedRows[0].langId;
    var langName =selectedRows[0].langName;
    var name=selectedRows[0].nameCat;


    $.ajax({
        url: '/blueseal/xhr/ManageProductCategoryTranslationAjaxController',
        method: 'get',
        data: {
            typeCall: 1,
            productCategoryId: productCategoryId,
            shopId: shopId,
            langId: langId
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawProduct = res;
        var u = 0;
        var responseOk = '';
        var description = '';
        var longDescription = '';
        $.each(rawProduct, function (k, v) {
            if (v.responseOk == '1') {
                description = v.description;
                longDescription = v.longDescription;
                name=v.name;
                u++;
                responseOk = 1;
            } else {
                responseOk = 2;

            }
        });

        if (responseOk == 1) {
            let bsModal = new $.bsModal('Modifica Traduzione ', {
                body: `<p>modifica Traduzione Categoria</p>
                <div class="row">
                 <div class="col-md-3">
                <div class="form-group form-group-default required">
                <label for="langName">Lingua</label>
                <input  type="text" id="langName" disabled name="langName" value="` + langName + `"/>
                </div>
                </div>
                  <div class="col-md-3">
                <div class="form-group form-group-default required">
                <label for="name">Nome Categoria</label>
                <input  type="text" id="name" disabled name="name" value="` + name + `"/>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group form-group-default required">
                <label for="description">Titolo Categoria</label>
                <input type="text"  id="description" name="description" value="` + description + `"/>
                </div>
                </div>
                </div>
                 <div class="row">
                 <div class="col-md-12"> 
                <div class="form-group form-group-default required">
                <label for="longDescription">Descrizione Categoria</label>
                 <textarea class="form-control" cols="400" rows="5" id="longDescription">`+longDescription+`</textarea>
                </div>
                </div> 
                </div>`
            });
            bsModal.addClass('modal-wide');
            bsModal.addClass('modal-high');
            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    productCategoryId: productCategoryId,
                    shopId: shopId,
                    langId:langId,
                    name:$('#name').val(),
                    description:$('#description').val(),
                    longDescription:$('#longDecription').val()
                };
                $.ajax({
                    method: 'POST',
                    url: '/blueseal/xhr/ManageProductCategoryTranslationAjaxController',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });

            });

        } else {
            let bsModal = new $.bsModal('Modifica Traduzione ', {
                body: `<p>Aggiungi Traduzione Categoria</p>
                <div class="row">
                 <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lang">Selettore Lingua
                                                </label>
                                                <select id="lang"
                                                        name="lang"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione la Lingua "
                                                        data-init-plugin="selectize">
                                                </select>

                                            </div>
                </div>
                  <div class="col-md-3">
                <div class="form-group form-group-default required">
                <label for="name">Nome Cateogria</label>
                <input  type="text" id="name" disabled name="name" value="` + name + `"/>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group form-group-default required">
                <label for="description">Titolo Categoria</label>
                <input type="text"  id="description" name="description" value="` + description + `"/>
                </div>
                </div>
                </div>
                 <div class="row">
                 <div class="col-md-12"> 
                <div class="form-group form-group-default required">
                <label for="longDescription">Descrizione Categoria</label>
                 <textarea class="form-control" cols="400" rows="5" id="longDescription">`+longDescription+`</textarea>
                </div>
                </div> 
                </div>`
            });
            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Lang',
                    condition: {isActive: 1}

                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('#lang');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: res2,
                });

            });
            bsModal.addClass('modal-wide');
            bsModal.addClass('modal-high');
            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    productCategoryId: productCategoryId,
                    shopId: shopId,
                    langId: $('#lang').val(),
                    name:$('#name').val(),
                    description:$('#description').val(),
                    longDescription:$('#longDecription').val()
                };
                $.ajax({
                    method: 'PUT',
                    url: '/blueseal/xhr/ManageProductCategoryTranslationAjaxController',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });

            });
        }
    });
});
$(document).on('bs.categoryTranslation.add', function () {


    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Puoi  selezionare solo un record "
        }).open();
        return false;
    }
    var productCategoryId = selectedRows[0].DT_RowId;
    var shopId = selectedRows[0].shopId;
    var langId = selectedRows[0].langId;
    var langName =selectedRows[0].langName;
    var name=selectedRows[0].nameCat;


    let bsModal = new $.bsModal('Aggiungi Traduzione ', {
        body: `<p>Aggiungi Traduzione Categoria</p>
                <div class="row">
                 <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lang">Selettore Lingua
                                                </label>
                                                <select id="lang"
                                                        name="lang"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione la Lingua "
                                                        data-init-plugin="selectize">
                                                </select>

                                            </div>
                </div>
                  <div class="col-md-3">
                <div class="form-group form-group-default required">
                <label for="name">Nome Categoria</label>
                <input  type="text" id="name"  name="name" value="` + name + `"/>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group form-group-default required">
                <label for="description">Titolo Categoria</label>
                <input type="text"  id="description" name="description" value=""/>
                </div>
                </div>
                </div>
                 <div class="row">
                 <div class="col-md-12"> 
                <div class="form-group form-group-default required">
                <label for="longDescription">Descrizione Categoria</label>
                <textarea class="form-control" cols="400" rows="5" id="longDescription"></textarea>
                </div>
                </div> 
                </div>`
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Lang',
            condition: {isActive: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#lang');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            productCategoryId: productCategoryId,
            shopId: shopId,
            langId: $('#lang').val(),
            name:$('#name').val(),
            description:$('#description').val(),
            longDescription:$('#longDecription').val()
        };
        $.ajax({
            method: 'PUT',
            url: '/blueseal/xhr/ManageProductCategoryTranslationAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });

    });
});

