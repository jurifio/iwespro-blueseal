;(function () {
    $(document).on('bs.product.sheet.model.cat.group.desc', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "Non hai selezionato niente"
            }).open();
            return false;
        }

        let catId = [];
        selectedRows.each(function (k) {
            catId.push(k.id);
        });


        let bsModal = new $.bsModal('Inserisci descrizione', {
            body: `<p>Descrizione</p>
                   <textarea id="desc-cat-group" style="width: 300px; height: 300px"></textarea>      
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                catId: catId,
                desc: $('#desc-cat-group').val(),
                field: 'desc'
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductModelPrototypeCategoryGroupAjaxManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });

    $(document).on('bs.product.sheet.model.cat.group.macro', function () {


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'ProductSheetModelPrototypeMacroCategoryGroup',
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#oldMacroCat');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res
            });
        });

        let bsModal = new $.bsModal('Associa categoria a macrocategoria', {
            body: `<p>Cerca categoria</p>
                   <div>
                   <label for="catName">Nome categoria</label>
                   <input type="text" id="catName">
                   <button class="btn-success" id="searchCatName">Cerca</button>
                   </div>
                   <div>
                   <select id="oldMacroCat">
                   <option disabled selected value>Seleziona un'opzione</option>
                   </select>
                   </div> 
                   <div id="catResult">
                   </div>
                   `
        });

        $('#searchCatName').on('click', function () {

            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/ProductModelPrototypeMacroCategoryGroupAjaxManage',
                data: {
                    cat: $('#catName').val(),
                },
                dataType: 'json'
            }).done(function (res) {
                let tableR = $('#catResult');

                tableR.empty();
                
                let table = `<table class="table"> 
                <thead> 
                <tr> 
                <th>Categoria</th> 
                <th>Macroategoria</th> 
                </tr> 
                </thead> 
                <tbody>`;
                
                $.each(res, function (k,v) {
                    table += `<tr>
                    <td style="padding-right: 3px">${v['catN']}</td> 
                    <td>${v['macroCatName']}</td> 
                    </tr>`
                });

                tableR.append(table);
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                cat: $('#catName').val(),
                macroCat: $('#oldMacroCat').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductModelPrototypeMacroCategoryGroupAjaxManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });


    });


    $(document).on('bs.product.sheet.model.cat.group.name', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Puoi inserire un nome alla volta"
            }).open();
            return false;
        }

        let catId = selectedRows[0].id;

        let bsModal = new $.bsModal('Inserisci il nuovo Nome', {
            body: `<p>Nome</p>
                   <input type="text" id="name-cat-group" style="width:70%"> 
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                catId: catId,
                name: $('#name-cat-group').val(),
                field: 'name'
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductModelPrototypeCategoryGroupAjaxManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });

    $(document).on('bs.product.sheet.model.cat.group.name.find.sub', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "seleziona almeno una riga"
            }).open();
            return false;
        }

        let ids = [];
        selectedRows.each(function (k, v) {
            ids.push(k.id)
        });

        let bsModal = new $.bsModal('Trova/sostituisci', {
            body: `
            <div>
                <p>Trova</p>
                <input type="text" id="find-name"> 
            </div>
            <div>
                <p>Sostituisci</p>
                <input type="text" id="sub-name"> 
            </div>`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: ids,
                find_name: $('#find-name').val(),
                sub_name: $('#sub-name').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductModelPrototypeCategoryGroupAjaxManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });

    $(document).on('bs.product.sheet.delete.model.cat.group', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "Non hai selezionato nessuna riga"
            }).open();
            return false;
        }

        let catId = [];
        selectedRows.each(function (k, v) {
            catId.push(k.id)
        });

        let bsModal = new $.bsModal('ELIMINA', {
            body: `<p>Procedere con l'eliminazione delle categorie?</p> 
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                catId: catId
            };
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductModelPrototypeCategoryGroupAjaxManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
})();